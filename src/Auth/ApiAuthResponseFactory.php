<?php

namespace Antriver\LaravelSiteScaffolding\Auth;

use Antriver\LaravelDatabaseSessionAuth\DatabaseSessionGuard;
use Antriver\LaravelSiteScaffolding\Auth\UserAuthenticator;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Users\UserPresenter;
use Auth;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;

class ApiAuthResponseFactory
{
    /**
     * @var Encrypter
     */
    private $encrypter;

    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @var UserPresenter
     */
    private $userPresenter;

    public function __construct(
        Encrypter $encrypter,
        UserAuthenticator $userAuthenticator,
        UserPresenter $userPresenter
    ) {
        $this->encrypter = $encrypter;
        $this->userPresenter = $userPresenter;
        $this->userAuthenticator = $userAuthenticator;
    }

    public function make(
        Request $request,
        UserInterface $user,
        string $token = null
    ) {
        if (empty($token)) {
            /** @var DatabaseSessionGuard $guard */
            $guard = Auth::guard('api');
            $token = $this->userAuthenticator->createUserDatabaseSessionToken($user, $request, $guard);
        }

        return [
            'user' => $this->userPresenter->present($user),
            'token' => $token,
        ];
    }
}
