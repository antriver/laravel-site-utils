<?php

namespace Antriver\LaravelSiteUtils\Users\Exceptions;

use Antriver\LaravelSiteUtils\Exceptions\ForbiddenHttpException;
use Antriver\LaravelSiteUtils\Exceptions\Traits\HasJwtTrait;
use Antriver\LaravelSiteUtils\Exceptions\Traits\HasUserTrait;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Antriver\LaravelSiteUtils\Users\UserPresenter;

class DeactivatedUserException extends ForbiddenHttpException
{
    use HasJwtTrait;
    use HasUserTrait;

    public function __construct(UserInterface $user)
    {
        parent::__construct();

        $this->setUser($user);
    }

    public function getData(): array
    {
        $arr = [
            'user' => $this->user ? app(UserPresenter::class)->present($this->user) : null,
        ];

        if ($this->jwt) {
            $arr['jwt'] = $this->jwt;
        }

        return $arr;
    }
}
