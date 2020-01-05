<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification;

use Antriver\LaravelSiteScaffolding\Tokens\TokenGenerator;
use Antriver\LaravelSiteScaffolding\Traits\GeneratesTokensTrait;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
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
class EmailVerificationManager extends AbstractRepository implements RepositoryInterface
{
    use GeneratesTokensTrait;

    /**
     * @param UserInterface $user
     *
     * @return \Illuminate\Database\Eloquent\Collection|EmailVerification[]
     */
    public function findPendingVerifications(UserInterface $user)
    {
        return EmailVerification::where('userId', $user->getId())->orderBy('id')->get();
    }

    /**
     * @param UserInterface $user
     *
     * @return EmailVerification
     */
    public function findLatestPendingVerification(UserInterface $user)
    {
        return EmailVerification::where('userId', $user->getId())->orderBy('id', 'DESC')->first();
    }

    /**
     * @param UserInterface $user
     *
     * @return EmailVerification
     */
    public function sendNewUserVerification(UserInterface $user)
    {
        $token = (new TokenGenerator())->generateToken();

        /** @var EmailVerification $emailVerification */
        $emailVerification = EmailVerification::create(
            [
                'userId' => $user->id,
                'email' => $user->email,
                'token' => $token,
            ]
        );

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
        $token = (new TokenGenerator())->generateToken();

        /** @var EmailVerification $emailVerification */
        $emailVerification = EmailVerification::create(
            [
                'userId' => $user->getId(),
                'email' => $email,
                'token' => $token,
                'isChange' => 1,
            ]
        );

        $this->sendEmail($emailVerification, $user, true);

        return $emailVerification;
    }

    /**
     * @param EmailVerification $emailVerification
     * @param UserInterface $user
     * @param bool $queued
     */
    public function sendEmail(EmailVerification $emailVerification, UserInterface $user, $queued = false)
    {
        if ($queued) {
            Mail::to($emailVerification->email)->queue(
                $this->createMessage($emailVerification, $user)
            );
        } else {
            Mail::to($emailVerification->email)->send(
                $this->createMessage($emailVerification, $user)
            );
        }
    }

    /**
     * @param EmailVerification $emailVerification
     * @param UserInterface $user
     * @param bool $queued
     */
    public function resendEmail(EmailVerification $emailVerification, UserInterface $user, $queued = false)
    {
        $this->sendEmail($emailVerification, $user, $queued);

        $emailVerification->resentAt = (new Carbon())->toDateTimeString();
        $this->persist($emailVerification);
    }

    /**
     * @param EmailVerification $emailVerification
     * @param UserRepositoryInterface $userRepository
     */
    public function verify(EmailVerification $emailVerification, UserRepositoryInterface $userRepository)
    {
        $user = $userRepository->findOrFail($emailVerification->userId);

        if ($emailVerification->isChange) {
            // Log the change
            UserEmailChange::create(
                [
                    'userId' => $user->getId(),
                    'oldEmail' => $user->getEmail(),
                    'newEmail' => $emailVerification->email,
                ]
            );
        }

        // Update the user's email address and set as verified
        $user->setEmail($emailVerification->email);
        $user->setEmailVerified(true);

        $userRepository->persist($user);

        $emailVerification->delete();
    }

    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return string
     */
    public function getModelClass()
    {
        return EmailVerification::class;
    }

    /**
     * @param EmailVerification $emailVerification
     * @param User $user
     *
     * @return EmailVerificationMail
     */
    protected function createMessage(EmailVerification $emailVerification, User $user)
    {
        return new EmailVerificationMail($emailVerification, $user);
    }
}
