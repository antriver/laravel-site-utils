<?php

namespace Antriver\LaravelSiteUtils\Users\Exceptions;

use Antriver\LaravelSiteUtils\EmailVerification\EmailVerification;
use Antriver\LaravelSiteUtils\Exceptions\ForbiddenHttpException;
use Antriver\LaravelSiteUtils\Exceptions\Traits\HasJwtTrait;
use Antriver\LaravelSiteUtils\Exceptions\Traits\HasUserTrait;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Antriver\LaravelSiteUtils\Users\UserPresenter;

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
            'user' => $this->user ? app(UserPresenter::class)->present($this->user) : null,
            'userEmail' => $this->user->email,
        ];
    }
}
