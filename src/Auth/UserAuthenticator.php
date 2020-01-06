<?php

namespace Antriver\LaravelSiteScaffolding\Auth;

use Antriver\LaravelDatabaseSessionAuth\DatabaseSessionGuard;
use Antriver\LaravelSiteScaffolding\Bans\BanRepository;
use Antriver\LaravelSiteScaffolding\Bans\Exceptions\BannedUserException;
use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationManager;
use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteScaffolding\Exceptions\InvalidInputException;
use Antriver\LaravelSiteScaffolding\Users\Exceptions\DeactivatedUserException;
use Antriver\LaravelSiteScaffolding\Users\Exceptions\UnverifiedUserException;
use Antriver\LaravelSiteScaffolding\Users\User;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Validation\RequestValidator;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

class UserAuthenticator
{
    use ThrottlesLogins;

    /**
     * @var BanRepository
     */
    private $banRepository;

    /**
     * @var EmailVerificationManager
     */
    private $emailVerificationManager;

    /**
     * @var EmailVerificationRepository
     */
    private $emailVerificationRepository;

    /**
     * @var JwtFactory
     */
    private $jwtFactory;

    /**
     * @var PasswordHasher
     */
    private $passwordHasher;

    /**
     * @var RequestValidator
     */
    private $requestValidator;

    public function __construct(
        BanRepository $banRepository,
        EmailVerificationManager $emailVerificationManager,
        EmailVerificationRepository $emailVerificationRepository,
        JwtFactory $jwtFactory,
        PasswordHasher $passwordHasher,
        RequestValidator $requestValidator
    ) {
        $this->banRepository = $banRepository;
        $this->emailVerificationManager = $emailVerificationManager;
        $this->emailVerificationRepository = $emailVerificationRepository;
        $this->jwtFactory = $jwtFactory;
        $this->passwordHasher = $passwordHasher;
        $this->requestValidator = $requestValidator;
    }

    /**
     * Check the given username and password. Return the matching user if there is one and that user
     * is allowed to login.
     *
     * @param Request $request
     * @param bool $canLoginIfUnverified
     *
     * @return UserInterface
     */
    public function validateLogin(
        Request $request,
        bool $canLoginIfUnverified = false
    ) {
        // Check input for sanity
        $this->requestValidator->validate(
            $request,
            [
                'username' => 'required',
                'password' => 'required',
            ]
        );

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        $credentials = $request->only(
            'username',
            'password'
        );

        try {
            $user = $this->findAccountByCredentials($credentials);
        } finally {
            if (empty($user)) {
                $this->incrementLoginAttempts($request);
            }
        }

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
        $this->clearLoginAttempts($request);
        $request->session()->regenerate();

        $token = $this->createUserDatabaseSessionToken($user, $request, Auth::guard('web'));

        $this->setSessionTokenCookie($token);

        return $token;
    }

    protected function setSessionTokenCookie(?string $token)
    {
        if (!empty($token)) {
            \Cookie::queue(
                \Cookie::make(
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
            \Cookie::queue(
                \Cookie::make(
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
    ) {
        $guard->login($user, $request);

        return $guard->getSessionId();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectSuccessfulLogin()
    {
        return redirect()->intended('/');
    }

    public function redirectUnverifiedUser(Request $request, UserInterface $user)
    {
        // Set user in session.
        $request->session()->put('newUser', $user);

        return redirect('/signup-complete');
    }

    /**
     * @param array $credentials
     *
     * @return UserInterface If success.
     * @throws InvalidInputException
     */
    protected function findAccountByCredentials(array $credentials): UserInterface
    {
        /** @var User $user */
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            throw new InvalidInputException(
                [
                    'username' => ['There is no account with that username.'],
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
     * Before we actually log the user in check this account is allowed to login.
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
    ) {
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

    /**
     * Get the login username to be used by the controller.
     * (Needed for ThrottlesLogins trait)
     *
     * @return string
     */
    protected function username()
    {
        return 'username';
    }
}
