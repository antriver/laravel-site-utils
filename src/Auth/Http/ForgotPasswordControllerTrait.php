<?php

namespace Antriver\LaravelSiteUtils\Auth\Http;

use Antriver\LaravelSiteUtils\Auth\Forgot\ForgottenPasswordManager;
use Antriver\LaravelSiteUtils\Auth\Forgot\PasswordResetTokenRepository;
use Antriver\LaravelSiteUtils\Http\Traits\ValidatesCaptchaTrait;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ForgotPasswordControllerTrait
{
    use ValidatesCaptchaTrait;

    /**
     * @api {post} /auth/forgot Send Password Reset Email
     *
     * @param Request $request
     * @param ForgottenPasswordManager $forgottenPasswordManager
     * @param PasswordResetTokenRepository $passwordResetTokenRepository
     * @param UserRepository $userRepository
     *
     * @return \Illuminate\Http\JsonResponse
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
