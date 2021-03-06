<?php

namespace Antriver\LaravelSiteUtils\Auth\Http;

use Antriver\LaravelDatabaseSessionAuth\DatabaseSessionGuard;
use Antriver\LaravelSiteUtils\Auth\ApiAuthResponseFactory;
use Antriver\LaravelSiteUtils\Auth\UserAuthenticator;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

trait AuthControllerTrait
{
    public function __construct()
    {
        $this->requireAuth(['only' => ['show', 'destroy']]);
    }

    /**
     * @api {get} /auth Get User For Session Token
     *
     * @param ApiAuthResponseFactory $apiAuthResponseFactory
     * @param Request $request
     * @param AuthManager $authManager
     * @param UserRepository $userRepository
     *
     * @return array
     * @throws AuthenticationException
     */
    public function show(
        ApiAuthResponseFactory $apiAuthResponseFactory,
        Request $request,
        AuthManager $authManager,
        UserRepository $userRepository
    ) {
        $user = $this->getRequestUser($request);
        $token = $request->input('token');

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
            config('auth.allow_unverified_user_login', false)
        );

        // An exception would have been thrown above if they cannot login, so it must be okay.
        // Now return a response containing a session token for them.
        $response = $apiAuthResponseFactory->make(
            $request,
            $user
        );

        return $this->response($response);
    }

    /**
     * @api {delete} /auth Logout (Delete Session Token)
     *
     * @return mixed
     */
    public function destroy() {
        $guard = \Auth::guard('api');
        $guard->logout();

        return $this->successResponse(true);
    }
}
