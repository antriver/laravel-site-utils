<?php

namespace Antriver\LaravelSiteScaffolding\Users;

use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationManager;
use Antriver\LaravelSiteScaffolding\Tokens\TokenGenerator;
use Antriver\LaravelSiteScaffolding\UserSettings\UserSettings;
use Antriver\LaravelSiteScaffolding\UserSettings\UserSettingsRepository;
use Antriver\LaravelSiteScaffolding\UserSocialAccounts\UserSocialAccount;
use Antriver\LaravelSiteScaffolding\UserSocialAccounts\UserSocialAccountRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Socialite\AbstractUser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

class UserService
{
    const USERNAME_REGEX = '/^[A-Za-z0-9_-]{1,30}$/';

    /**
     * @var EmailVerificationManager
     */
    private $emailVerificationManager;

    /**
     * @var PasswordHasher
     */
    private $passwordHasher;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var UserSettingsRepository
     */
    private $userSettingsRepository;

    /**
     * @var UsernameFactory
     */
    private $usernameFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserSocialAccountRepository
     */
    private $userSocialAccountRepository;

    public function __construct(
        EmailVerificationManager $emailVerificationManager,
        PasswordHasher $passwordHasher,
        TokenGenerator $tokenGenerator,
        UsernameFactory $usernameFactory,
        UserRepository $userRepository,
        UserSettingsRepository $userSettingsRepository,
        UserSocialAccountRepository $userSocialAccountRepository
    ) {
        $this->emailVerificationManager = $emailVerificationManager;
        $this->passwordHasher = $passwordHasher;
        $this->tokenGenerator = $tokenGenerator;
        $this->usernameFactory = $usernameFactory;
        $this->userRepository = $userRepository;
        $this->userSettingsRepository = $userSettingsRepository;
        $this->userSocialAccountRepository = $userSocialAccountRepository;
    }

    /**
     * Takes a User from Laravel Socialite and either returns the existing user that logged in via
     * that account, or creates a new user.
     *
     * @param string $service
     * @param AbstractUser|\Laravel\Socialite\One\User|\Laravel\Socialite\Two\User $socialUser
     * @param Request $request
     *
     * @return User
     * @throws \Exception
     */
    public function handleSocialLogin($service, AbstractUser $socialUser, Request $request)
    {
        $account = $this->returnUserSocialAccountFromSocialUser($service, $socialUser);

        if ($account->userId && $user = $this->userRepository->find($account->userId)) {
            // Existing user
        } else {
            $data = [
                'username' => $this->usernameFactory->makeUsernameFromSocialUser($socialUser),
                'email' => $socialUser->getEmail(),
            ];

            // TODO: Save avatar from $socialUser->avatar;

            $user = $this->createUser($data, $request);
            $account->userId = $user->id;
        }

        $account->setUpdatedAt(new Carbon());
        $this->userSocialAccountRepository->persist($account);

        return $user;
    }

    /**
     * Link the given Laravel Socialite user to an existing User.
     *
     * @param string $service
     * @param AbstractUser|\Laravel\Socialite\One\User|\Laravel\Socialite\Two\User $socialUser
     * @param User $user
     *
     * @return UserSocialAccount
     * @throws \Exception
     */
    public function handleSocialLink($service, AbstractUser $socialUser, User $user)
    {
        $account = $this->returnUserSocialAccountFromSocialUser($service, $socialUser);
        if ($account->userId && $account->userId != $user->id) {
            throw new BadRequestHttpException(
                "This {$service} account is already linked to another user. 
                (Tip: you can logout then login via {$service} to get to that account, 
                then remove the link from the Settings page.)"
            );
        }

        $account->userId = $user->id;
        $this->userSocialAccountRepository->persist($account);

        return $account;
    }

    /**
     * Create or return the existing UserSocialAccount for this social user.
     * Sets the new values on the UserSocialAccount from the user's data, but does not persist the changes.
     *
     * @param string $service
     * @param AbstractUser|\Laravel\Socialite\One\User|\Laravel\Socialite\Two\User $socialUser
     *
     * @return UserSocialAccount
     * @throws \Exception
     */
    private function returnUserSocialAccountFromSocialUser($service, AbstractUser $socialUser)
    {
        $socialUserId = $socialUser->getId();
        if (empty($socialUserId) && $socialUser->getNickname()) {
            $socialUserId = $socialUser->getNickname();
        }
        if (empty($socialUserId)) {
            throw new \Exception("Service did not return a userId or nickname - cannot continue.");
        }

        $account = $this->userSocialAccountRepository->findByServiceUserIdOrCreate($service, $socialUserId);

        $account->token = $socialUser->token;
        $account->expiresAt = !empty($socialUser->expiresIn)
            ? (new Carbon())->addSeconds($socialUser->expiresIn)->toDateTimeString()
            : null;
        $account->refreshToken = !empty($socialUser->refreshToken) ? $socialUser->refreshToken : null;
        $account->tokenSecret = !empty($socialUser->tokenSecret) ? $socialUser->tokenSecret : null;
        $account->nickname = $socialUser->getNickname();
        $account->name = $socialUser->getName();
        $account->email = $socialUser->getEmail();

        return $account;
    }

    /**
     * Create a new user from he given data. Saves and returns them.
     *
     * @param array $data
     * @param Request|null $request
     * @param bool $verifyEmail
     *
     * @return User
     * @throws \Exception
     */
    public function createUser($data, Request $request = null, bool $verifyEmail = true)
    {
        $userClass = $this->userRepository->getModelClass();

        /** @var User $user */
        $user = new $userClass($data);

        if (!empty($data['password'])) {
            $this->setUserPassword($user, $data['password']);
        }

        if (!$verifyEmail) {
            $user->setEmailVerified(true);
        }

        if (!$this->userRepository->persist($user)) {
            throw new \Exception("Unable to save user.");
        }

        $this->createUserSettings($user, $request);

        if ($verifyEmail) {
            // Send verification email.
            $this->emailVerificationManager->sendNewUserVerification($user);
        }

        return $user;
    }

    public function setUserPassword(User $user, string $password)
    {
        $user->password = $this->passwordHasher->generateHash($password);
    }

    protected function createUserSettings(UserInterface $user, Request $request)
    {
        $settings = new UserSettings(
            [
                'userId' => $user->getId(),
            ]
        );

        if ($request) {
            $settings->signupIp = $request->getClientIp();
        }

        $settings->emailKey = $this->tokenGenerator->generateToken();

        $this->userSettingsRepository->persist($settings);
    }
}
