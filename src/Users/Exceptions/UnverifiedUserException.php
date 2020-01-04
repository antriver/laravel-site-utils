<?php

namespace Antriver\LaravelSiteScaffolding\Users\Exceptions;

use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification;
use Antriver\LaravelSiteScaffolding\Exceptions\ForbiddenHttpException;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Users\UserPresenterInterface;

class UnverifiedUserException extends ForbiddenHttpException
{
    /**
     * @var EmailVerification|null
     */
    private $emailVerification;

    /**
     * JWT may be needed for the user to start re-verification or re-send an email.
     *
     * @var string|null
     */
    private $jwt;

    /**
     * @var string|null
     */
    private $token;

    /**
     * @var UserInterface
     */
    private $user;

    public function __construct(UserInterface $user, ?EmailVerification $emailVerification)
    {
        $message = 'This account has not yet verified their email address.';

        parent::__construct($message);

        $this->emailVerification = $emailVerification;
        $this->user = $user;
    }

    /**
     * @param null|string $jwt
     */
    public function setJwt(?string $jwt): void
    {
        $this->jwt = $jwt;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getData(): array
    {
        return [
            'emailVerification' => $this->emailVerification ? $this->emailVerification->toArray() : null,
            'jwt' => $this->jwt,
            'user' => $this->user ? app(UserPresenterInterface::class)->present($this->user) : null,
            'userEmail' => $this->user->email,
        ];
    }
}
