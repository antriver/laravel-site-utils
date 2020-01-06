<?php

namespace Antriver\LaravelSiteScaffolding\Auth\Http;

use Antriver\LaravelSiteScaffolding\Auth\ApiAuthResponseFactory;
use Antriver\LaravelSiteScaffolding\Auth\JwtFactory;
use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationManager;
use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteScaffolding\Http\Traits\ValidatesCaptchaTrait;
use Antriver\LaravelSiteScaffolding\Users\UserService;
use Antriver\LaravelSiteScaffolding\Users\ValidatesUserCredentialsTrait;
use Illuminate\Http\Request;

trait RegisterControllerTrait
{
    use ValidatesCaptchaTrait;
    use ValidatesUserCredentialsTrait;

    /**
     * @param ApiAuthResponseFactory $apiAuthResponseFactory
     * @param EmailVerificationManager $emailVerificationManager
     * @param JwtFactory $jwtFactory
     * @param Request $request
     * @param UserService $userService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        ApiAuthResponseFactory $apiAuthResponseFactory,
        JwtFactory $jwtFactory,
        Request $request,
        UserService $userService,
        EmailVerificationRepository $emailVerificationRepository
    ) {
        $this->validateRequestCaptcha($request);

        $this->validate(
            $request,
            [
                'username' => $this->getUsernameValidationRules(),
                'email' => $this->getEmailValidationRules(),
                'password' => $this->getPasswordValidationRules(true),
            ]
        );

        $data = $request->only(['username', 'email', 'password']);

        $user = $userService->createUser($data, $request);

        if (config('auth.allow_unverified_user_login')) {
            $response = $apiAuthResponseFactory->make(
                $request,
                $user
            );
        } else {
            // Return a JWT so they can re-send the verification email.
            $jwt = $jwtFactory->generateToken($user);

            // Return the pending email verification
            $emailVerification = $emailVerificationRepository->findLatestPendingVerification($user);

            $response = [
                'emailVerification' => $emailVerification,
                'jwt' => $jwt,
                'user' => $user,
            ];
        }

        return $this->response($response);
    }
}
