<?php

namespace Antriver\LaravelSiteScaffolding\Auth\Http;

use Amirite\Http\Controllers\Traits\AuthenticatesUsersTrait;
use Antriver\LaravelSiteScaffolding\Bans\BanRepository;
use Antriver\LaravelSiteScaffolding\Bans\BanRepositoryInterface;
use Antriver\LaravelSiteScaffolding\Users\Exceptions\UnverifiedUserException;
use Antriver\LaravelSiteScaffolding\Users\UserPresenterInterface;
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
     * @param AuthManager $authManager
     * @param UserPresenterInterface $userPresenter
     * @param UserRepositoryInterface $userRepository
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    public function show(
        Request $request,
        AuthManager $authManager,
        UserPresenterInterface $userPresenter,
        UserRepositoryInterface $userRepository
    ) {
        $token = $request->input('token');

        $guard = $authManager->guard('api');
        $userId = $guard->findUserIdBySessionId($token);
        if (!$userId) {
            throw new AuthenticationException();
        }
        $user = $userRepository->findOrFail($userId);

        $response = [
            'user' => $userPresenter->present($user),
            'token' => $token,
        ];

        return $this->response($response);
     }

     /**
     * @api {post} /auth Login (Create Session Token)
     *
     * @apiDesc Validate login credentials and start a session for the user.
     * Returns an API token for the user.
     *
     * @param ApiAuthResponseFactory $apiAuthResponseFactory
     * @param BanRepository $banRepository
     * @param DeactivationManager $deactivationManager
     * @param JwtFactory $jwtFactory
     * @param PasswordHasher $passwordHasher
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws DeactivatedUserException
     * @throws UnverifiedUserException
     */
    public function store(
        ApiAuthResponseFactory $apiAuthResponseFactory,
        BanRepositoryInterface $banRepository,
        DeactivationManager $deactivationManager,
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

        } catch (DeactivatedUserException $exception) {
            // Credentials were valid but user is deactivated.

            // TODO: Remove this. v5 calls the reactivate endpoint.
            if ($request->input('reactivate')) {
                $user = $exception->getUser();
                $deactivationManager->reactivateUser($user);
            } else {
                $exception->setJwt($jwtFactory->generateToken($exception->getUser()));
                throw $exception;
            }
        }

        $response = $apiAuthResponseFactory->make(
            $request,
            $user
        );

        return $this->response($response);
    }
}
