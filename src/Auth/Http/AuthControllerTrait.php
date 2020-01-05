<?php

namespace Antriver\LaravelSiteScaffolding\Auth\Http;

use Antriver\LaravelSiteScaffolding\Auth\UserAuthenticator;
use Antriver\LaravelSiteScaffolding\Users\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

trait AuthControllerTrait
{
    public function __construct()
    {
        $this->requireAuth(['only' => 'show']);
    }

    /**
     * @api {get} /auth Get User For Session Token
     *
     * @param ApiAuthResponseFactory $apiAuthResponseFactory
     * @param Request $request
     * @param AuthManager $authManager
     * @param UserRepositoryInterface $userRepository
     *
     * @return array
     * @throws AuthenticationException
     */
    public function show(
        ApiAuthResponseFactory $apiAuthResponseFactory,
        Request $request,
        AuthManager $authManager,
        UserRepositoryInterface $userRepository
    ) {
        $token = $request->input('token');

        $guard = $authManager->guard('api');
        $userId = $guard->findUserIdBySessionId($token);
        if (!$userId) {
            throw new AuthenticationException();
        }
        $user = $userRepository->findOrFail($userId);

        $response = $apiAuthResponseFactory->make(
            $request,
            $user,
            $token
        );

        return $this->response($response);
    }

    /**
     * @api {post} /auth Login (Create Session Token)
     *
     * @apiDesc Validate login credentials and start a session for the user.
     * Returns an API token for the user.
     *
     * @param ApiAuthResponseFactory $apiAuthResponseFactory
     * @param Request $request
     *
     * @param UserAuthenticator $userAuthenticator
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        ApiAuthResponseFactory $apiAuthResponseFactory,
        Request $request,
        UserAuthenticator $userAuthenticator
    ) {
        // Check the user can login.
        $user = $userAuthenticator->validateLogin(
            $request,
            config('auth.allow_unverified_user_login')
        );

        // An exception would have been thrown above if they cannot login, so it must be okay.
        // Now return a response containing a session token for them.
        $response = $apiAuthResponseFactory->make(
            $request,
            $user
        );

        return $this->response($response);
    }
}
