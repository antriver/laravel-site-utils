<?php

namespace Antriver\LaravelSiteScaffolding\Users\Exceptions;

use Antriver\LaravelSiteScaffolding\Exceptions\ForbiddenHttpException;
use Antriver\LaravelSiteScaffolding\Exceptions\Traits\HasJwtTrait;
use Antriver\LaravelSiteScaffolding\Exceptions\Traits\HasUserTrait;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Users\UserPresenterInterface;

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
            'user' => $this->user ? app(UserPresenterInterface::class)->present($this->user) : null,
        ];

        if ($this->jwt) {
            $arr['jwt'] = $this->jwt;
        }

        return $arr;
    }
}
