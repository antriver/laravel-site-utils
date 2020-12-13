<?php

namespace Antriver\LaravelSiteUtils\EmailVerification;

use Antriver\LaravelSiteUtils\Mail\Base\ExtendedMailable;
use Antriver\LaravelSiteUtils\Users\UserInterface;

class EmailVerificationMail extends ExtendedMailable
{
    /**
     * @var EmailVerification
     */
    private $emailVerification;

    /**
     * @param EmailVerification $emailVerification
     * @param UserInterface $user
     */
    public function __construct(EmailVerification $emailVerification, UserInterface $user)
    {
        $this->emailVerification = $emailVerification;
        $this->setRecipient($user);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this
            ->success()
            ->subject("Please verify your email address")
            ->line(
                "We just need to check that we got the correct email address for your account."
            )
            ->action('Verify Email', $this->emailVerification->getUrl());

        return $this->view('emails.layouts.default');
    }
}
