<?php

namespace Antriver\LaravelSiteUtils\Auth;

use Antriver\LaravelDatabaseSessionAuth\DatabaseSessionGuard;
use Antriver\LaravelSiteUtils\Bans\BanRepository;
use Antriver\LaravelSiteUtils\Bans\Exceptions\BannedUserException;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationManager;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteUtils\Exceptions\InvalidInputException;
use Antriver\LaravelSiteUtils\Users\Exceptions\DeactivatedUserException;
use Antriver\LaravelSiteUtils\Users\Exceptions\UnverifiedUserException;
use Antriver\LaravelSiteUtils\Users\PasswordHasher;
use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Antriver\LaravelSiteUtils\Validation\RequestValidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class UserAuthenticator
{
    public function __construct(
        private BanRepository $banRepository,
        private EmailVerificationRepository $emailVerificationRepository,
        private JwtFactory $jwtFactory,
        private PasswordHasher $passwordHasher,
        private RequestValidator $requestValidator
    ) {
    }

    /**
     * Check the given username and password. Return the matching user if there is one and that user
     * is allowed to log in.
     */
    public function validateLogin(
        Request $request,
        bool $canLoginIfUnverified = false
    ): UserInterface {
        // Check input for sanity
        $this->requestValidator->validate(
            $request,
            [
                'username' => 'required',
                'password' => 'required',
            ]
        );

        $credentials = $request->only(
            'username',
            'password'
        );

        $user = $this->findAccountByCredentials($credentials);

        // Credentials are valid. Throw an exception if the user is deactivated or banned.
        $this->ensureAccountCanLogin(
            $user,
            $canLoginIfUnverified
        );

        return $user;
    }

    /**
     * Start a new session with the given user logged in.
     * Returns the access token.
     *
     * @param UserInterface $user
     * @param Request $request
     *
     * @return string
     */
    public function setSessionCookieAndLoginToWeb(UserInterface $user, Request $request): string
    {
        $request->session()->regenerate();

        $token = $this->createUserDatabaseSessionToken(
            $user,
            $request,
            Auth::guard('web')
        );

        $this->setSessionTokenCookie($token);

        return $token;
    }

    protected function setSessionTokenCookie(?string $token): void
    {
        if (!empty($token)) {
            Cookie::queue(
                Cookie::make(
                    config('app.session_cookie_name'),
                    $token,
                    2628000,
                    config('app.session_cookie_path'),
                    config('app.cookie_domain'),
                    true,
                    false
                )
            );
        } else {
            Cookie::queue(
                Cookie::make(
                    config('app.session_cookie_name'),
                    '',
                    -2628000,
                    config('app.session_cookie_path'),
                    config('app.cookie_domain'),
                    true,
                    false
                )
            );
        }
    }

    public function createUserDatabaseSessionToken(
        UserInterface $user,
        Request $request,
        DatabaseSessionGuard $guard
    ): ?string {
        $guard->login($user, $request);

        return $guard->getSessionId();
    }

    public function redirectSuccessfulLogin(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->intended('/');
    }

    public function redirectUnverifiedUser(Request $request, UserInterface $user): \Illuminate\Http\RedirectResponse
    {
        // Set user in session.
        $request->session()->put('newUser', $user);

        return redirect('/signup-complete');
    }

    protected function findAccountByCredentials(array $credentials): UserInterface
    {
        /** @var User $user */
        $user = User::where(function (Builder $builder) use ($credentials) {
            return $builder->where('username', $credentials['username'])
                ->orWhere('email', $credentials['username']);
        })->first();

        if (!$user) {
            throw new InvalidInputException(
                [
                    'username' => ['There is no account with that username or email.'],
                ]
            );
        }

        if (!$this->passwordHasher->verify($credentials['password'], $user, 'password')) {
            throw new InvalidInputException(
                [
                    'password' => ['That password is not correct.'],
                ]
            );
        }

        return $user;
    }

    /**
     * Called after the credentials have been checked, so we know the user is who they claim to be.
     * Before we actually log the user in check this account is allowed to log in.
     * - Not banned
     * - Not deactivation
     * - Email verified (if enabled)
     *
     * @param UserInterface $user
     * @param bool $canLoginIfUnverified
     *
     * @return bool
     */
    public function ensureAccountCanLogin(
        UserInterface $user,
        bool $canLoginIfUnverified = false
    ): bool {
        if ($ban = $this->banRepository->findCurrentForUser($user)) {
            throw new BannedUserException($ban, $user);
        }

        if ($user->isDeactivated()) {
            $exception = new DeactivatedUserException($user);

            // Add a JWT to the exception so the user can reactivate.
            $exception->setJwt($this->jwtFactory->generateToken($exception->getUser()));

            throw $exception;
        }

        // We now allow unverified users to login.
        if (!$canLoginIfUnverified && !$user->isEmailVerified()) {
            // Add the pending email verification to the exception if there is one.
            // Note there may not be one if the user has been de-verified due to an email bounce etc.
            // In which case they should be prompted to change their email address.
            $emailVerification = $this->emailVerificationRepository->findLatestPendingVerification($user);

            $exception = new UnverifiedUserException($user, $emailVerification);

            // Add a JWT to the exception so the user can resend the request or change their email address.
            $exception->setJwt($this->jwtFactory->generateToken($exception->getUser()));

            throw $exception;
        }

        return true;
    }
}
