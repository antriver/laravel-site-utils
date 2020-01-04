<?php

namespace Amirite\Http\Controllers\Traits;

use Amirite\Exceptions\BannedUserException;
use Amirite\Exceptions\DeactivatedUserException;
use Amirite\Exceptions\InvalidInputException;
use Amirite\Exceptions\UnverifiedUserException;
use Amirite\Libraries\Users\EmailVerificationManager;
use Amirite\Models\User;
use Amirite\Repositories\BanRepository;
use Antriver\LaravelDatabaseSessionAuth\DatabaseSessionGuard;
use Antriver\LaravelSiteScaffolding\Bans\BanRepositoryInterface;
use Antriver\LaravelSiteScaffolding\Exceptions\InvalidInputException;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Users\ValidatesUserCredentialsTrait;
use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

trait AuthenticatesUsersTrait
{
    use ValidatesUserCredentialsTrait;
    use ThrottlesLogins;

    /**
     * @param Request $request
     * @param PasswordHasher $passwordHasher
     * @param BanRepositoryInterface $banRepository
     *
     * @return UserInterface|array User if success, array of errors otherwise.
     */
    protected function validateLogin(
        Request $request,
        PasswordHasher $passwordHasher,
        BanRepositoryInterface $banRepository
    ) {
        // Check input for sanity
        $this->validate(
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
            $user = $this->findAccountByCredentials($credentials, $passwordHasher);
        } finally {
            if (empty($user)) {
                $this->incrementLoginAttempts($request);
            }
        }

        // Credentials are valid. Throw an exception if the user is deactivated or banned.
        $this->ensureAccountCanLogin($user, $banRepository);

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
    protected function setSessionCookieAndLoginToWeb(UserInterface $user, Request $request): string
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

    protected function createUserDatabaseSessionToken(UserInterface $user, Request $request, DatabaseSessionGuard $guard)
    {
        $guard->login($user, $request);

        return $guard->getSessionId();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectSuccessfulLogin()
    {
        return redirect()->intended('/');
    }

    protected function redirectUnverifiedUser(Request $request, UserInterface $user)
    {
        // Set user in session.
        $request->session()->put('newUser', $user);

        return redirect('/signup-complete');
    }

    /**
     * @param array $credentials
     * @param PasswordHasher $passwordHasher
     *
     * @return UserInterface If success.
     * @throws InvalidInputException
     */
    protected function findAccountByCredentials(array $credentials, PasswordHasher $passwordHasher): UserInterface
    {
        /** @var User $user */
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            throw new InvalidInputException(
                ['username' => ['There is no account with that username.']]
            );
        }

        if (!$passwordHasher->verify($credentials['password'], $user, 'password')) {
            throw new InvalidInputException(
                ['password' => ['That password is not correct.']]
            );
        }

        return $user;
    }

    /**
     * @param User $user
     * @param BanRepository $banRepository
     *
     * @return bool
     * @throws BannedUserException
     * @throws DeactivatedUserException
     * @throws UnverifiedUserException
     */
    protected function ensureAccountCanLogin(User $user, BanRepositoryInterface $banRepository)
    {
        if ($ban = $banRepository->findCurrentForUser($user)) {
            throw new BannedUserException($ban, $user);
        }

        if ($user->isDeactivated()) {
            throw new DeactivatedUserException($user);
        }

        // We now allow unverified users to login.
        /*if (!$user->emailVerified) {
            // Add the pending email verification to the exception if there is one.
            // Note there may not be one if the user has been de-verified due to an email bounce etc.
            // In which case they should be prompted to change their email address.
            $verificationManager = app(EmailVerificationManager::class);
            $emailVerification = $verificationManager->findLatestPendingVerification($user);
            throw new UnverifiedUserException($user, $emailVerification);
        }*/

        return true;
    }

    /**
     * Get the login username to be used by the controller.
     * (Needed for ThrottlesLogins trait)
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }
}
