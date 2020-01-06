<?php

namespace Antriver\LaravelSiteScaffolding\Auth\Http;

use Antriver\LaravelSiteScaffolding\Auth\Forgot\ForgottenPasswordManager;
use Antriver\LaravelSiteScaffolding\Auth\Forgot\PasswordResetTokenRepository;
use Antriver\LaravelSiteScaffolding\Http\Controllers\Base\AbstractApiController;
use Antriver\LaravelSiteScaffolding\Http\Traits\ValidatesCaptchaTrait;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ForgotController extends AbstractApiController
{
    use ValidatesCaptchaTrait;

    /**
     * @api {post} /auth/forgot Send Password Reset Email
     *
     * @param Request $request
     * @param PasswordResetTokenRepository $passwordResetTokenRepository
     * @param UserRepository $userRepository
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestHttpException
     */
    public function store(
        Request $request,
        ForgottenPasswordManager $forgottenPasswordManager,
        PasswordResetTokenRepository $passwordResetTokenRepository,
        UserRepository $userRepository
    ) {
        $this->validateRequestCaptcha($request);

        $this->validate(
            $request,
            [
                'email' => 'required',
            ]
        );

        $user = $userRepository->findOneBy('email', $request->input('email'));
        if (!$user) {
            throw new BadRequestHttpException("There is no user with that email address.");
        }

        // Create a reset token.
        $token = $passwordResetTokenRepository->create($user);

        // Send the user the email.
        $forgottenPasswordManager->sendForgottenPasswordEmail($user, $token);

        return $this->successResponse(true);
    }
}
