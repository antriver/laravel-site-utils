<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification;

use Antriver\LaravelSiteScaffolding\EmailVerification\Events\EmailBouncedEvent;
use Antriver\LaravelSiteScaffolding\EmailVerification\Events\EmailVerifiedEvent;
use Antriver\LaravelSiteScaffolding\Tokens\TokenGenerator;
use Antriver\LaravelSiteScaffolding\Traits\GeneratesTokensTrait;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Antriver\LaravelSiteScaffolding\Users\UserRepositoryInterface;
use Carbon\Carbon;
use Mail;
use Tmd\LaravelRepositories\Base\AbstractRepository;
use Tmd\LaravelRepositories\Interfaces\RepositoryInterface;

/**
 * Handles sending verification emails to new users, or when an existing user changes their email address.
 * New users are created with the email set on the User, and emailVerified = 0.
 * Email changes are not set on the User until verified.
 *
 * @method EmailVerification find($key)
 * @method EmailVerification findOrFail($key)
 *
 * @package Antriver\LaravelSiteScaffolding\Libraries\Users
 */
class EmailVerificationManager
{
    /**
     * @var TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var EmailVerificationRepository
     */
    private $emailVerificationRepository;

    public function __construct(
        EmailVerificationRepository $emailVerificationRepository,
        TokenGenerator $tokenGenerator,
        UserRepository $userRepository
    ) {
        $this->tokenGenerator = $tokenGenerator;
        $this->userRepository = $userRepository;
        $this->emailVerificationRepository = $emailVerificationRepository;
    }


    /**
     * @param UserInterface $user
     *
     * @return EmailVerification
     */
    public function sendNewUserVerification(UserInterface $user)
    {
        $token = $this->tokenGenerator->generateToken();

        /** @var EmailVerification $emailVerification */
        $emailVerification = new EmailVerification(
            [
                'userId' => $user->id,
                'email' => $user->email,
                'token' => $token,
                'type' => EmailVerification::TYPE_SIGNUP,
            ]
        );
        $this->emailVerificationRepository->persist($emailVerification);

        $this->sendEmail($emailVerification, $user);

        return $emailVerification;
    }

    /**
     * @param UserInterface $user
     * @param $email
     *
     * @return EmailVerification
     */
    public function sendEmailChangeVerification(UserInterface $user, $email)
    {
        $token = $this->tokenGenerator->generateToken();

        /** @var EmailVerification $emailVerification */
        $emailVerification = new EmailVerification(
            [
                'userId' => $user->getId(),
                'email' => $email,
                'token' => $token,
                'isChange' => 1,
            ]
        );
        $this->emailVerificationRepository->persist($emailVerification);

        $this->sendEmail($emailVerification, $user);

        return $emailVerification;
    }

    /**
     * @param EmailVerification $emailVerification
     * @param UserInterface $user
     */
    public function sendEmail(EmailVerification $emailVerification, UserInterface $user)
    {
        Mail::to($emailVerification->email)->send(
            $this->createMessage($emailVerification, $user)
        );
    }

    /**
     * @param EmailVerification $emailVerification
     * @param UserInterface $user
     * @param bool $queued
     */
    public function resendEmail(EmailVerification $emailVerification, UserInterface $user)
    {
        $this->sendEmail($emailVerification, $user);

        $emailVerification->resentAt = (new Carbon())->toDateTimeString();
        $this->emailVerificationRepository->persist($emailVerification);
    }

    /**
     * @param EmailVerification $emailVerification
     */
    public function verify(EmailVerification $emailVerification)
    {
        $user = $this->userRepository->findOrFail($emailVerification->userId);
        $newEmail = $emailVerification->email;
        $oldEmail = $user->email;

        // Update the user's email address and set them as verified
        $user->setEmail($newEmail);
        $user->setEmailVerified(true);
        $user->setEmailBounced(false);
        $this->userRepository->persist($user);

        if ($emailVerification->isChange) {
            // Log the change
            UserEmailChange::create(
                [
                    'userId' => $user->getId(),
                    'oldEmail' => $oldEmail,
                    'newEmail' => $newEmail,
                ]
            );
        }

        $this->emailVerificationRepository->remove($emailVerification);

        event(new EmailVerifiedEvent($user, $newEmail));
    }


    /**
     * @param EmailVerification $emailVerification
     * @param UserInterface $user
     *
     * @return EmailVerificationMail
     */
    protected function createMessage(EmailVerification $emailVerification, UserInterface $user)
    {
        return new EmailVerificationMail($emailVerification, $user);
    }

    /**
     * Generally called when receiving an SES bounce or complaint.
     * Unset the user's email as verified, and force them to re-verify their email.
     *
     * @param string $email
     */
    public function markUserEmailBounced(string $email)
    {
        /** @var UserInterface $user */
        $user = $this->userRepository->findOneBy('email', $email);
        if ($user) {
            $user->setEmailBounced(true);
            $this->userRepository->persist($user);

            event(new EmailBouncedEvent($user, $email));
        }
    }

    public function logBounce(string $type, string $email, string $message)
    {
        $user = $this->userRepository->findOneBy('email', $email);

        $log = new EmailBounce(
            [
                'type' => $type,
                'email' => $email,
                'userId' => $user ? $user->id : null,
                'message' => $message,
            ]
        );
        $log->save();
    }
}
