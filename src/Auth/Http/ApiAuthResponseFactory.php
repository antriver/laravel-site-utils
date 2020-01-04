<?php

namespace Antriver\LaravelSiteScaffolding\Auth\Http;

use Amirite\Http\Controllers\Traits\AuthenticatesUsersTrait;
use Antriver\LaravelDatabaseSessionAuth\DatabaseSessionGuard;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Users\UserPresenterInterface;
use Auth;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;

class ApiAuthResponseFactory
{
    use AuthenticatesUsersTrait;

    /**
     * @var Encrypter
     */
    private $encrypter;

    /**
     * @var UserPresenterInterface
     */
    private $userPresenter;

    public function __construct(
        Encrypter $encrypter,
        UserPresenterInterface $userPresenter

    ) {
        $this->encrypter = $encrypter;
        $this->userPresenter = $userPresenter;
    }

    public function make(
        Request $request,
        UserInterface $user
    ) {
        /** @var DatabaseSessionGuard $guard */
        $guard = Auth::guard('api');
        $token = $this->createUserDatabaseSessionToken($user, $request, $guard);

        return [
            'user' => $this->userPresenter->present($user),
            'token' => $token,
        ];
    }
}
