<?php

namespace Antriver\LaravelSiteScaffolding\Auth\Http;

use Antriver\LaravelSiteScaffolding\Auth\JwtFactory;
use Antriver\LaravelSiteScaffolding\Bans\BanRepositoryInterface;
use Antriver\LaravelSiteScaffolding\Users\Exceptions\UnverifiedUserException;
use Antriver\LaravelSiteScaffolding\Users\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

trait AuthControllerTrait
{
    use AuthenticatesUsersTrait;

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
            $user
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
     * @param BanRepositoryInterface $banRepository
     * @param JwtFactory $jwtFactory
     * @param PasswordHasher $passwordHasher
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws UnverifiedUserException
     */
    public function store(
        ApiAuthResponseFactory $apiAuthResponseFactory,
        BanRepositoryInterface $banRepository,
        JwtFactory $jwtFactory,
        PasswordHasher $passwordHasher,
        Request $request
    ) {
        try {
            $user = $this->validateLogin($request, $passwordHasher, $banRepository);
        } catch (UnverifiedUserException $exception) {
            // Credentials were valid but email is not verified.
            // Add a JWT to the response so user can resend or change email.
            $exception->setJwt($jwtFactory->generateToken($exception->getUser()));

            throw $exception;

        }

        $response = $apiAuthResponseFactory->make(
            $request,
            $user
        );

        return $this->response($response);
    }
}
