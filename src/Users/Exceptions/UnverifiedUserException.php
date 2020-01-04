<?php

namespace Antriver\LaravelSiteScaffolding\Users\Exceptions;

use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification;
use Antriver\LaravelSiteScaffolding\Exceptions\ForbiddenHttpException;
use Antriver\LaravelSiteScaffolding\Exceptions\Traits\HasJwtTrait;
use Antriver\LaravelSiteScaffolding\Exceptions\Traits\HasUserTrait;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Users\UserPresenterInterface;

class UnverifiedUserException extends ForbiddenHttpException
{
    use HasJwtTrait;
    use HasUserTrait;

    /**
     * @var EmailVerification|null
     */
    private $emailVerification;

    public function __construct(UserInterface $user, ?EmailVerification $emailVerification)
    {
        $message = 'This account has not yet verified their email address.';

        parent::__construct($message);

        $this->emailVerification = $emailVerification;
        $this->setUser($user);
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
