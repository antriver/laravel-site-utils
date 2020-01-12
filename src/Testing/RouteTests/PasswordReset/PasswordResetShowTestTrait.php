<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\PasswordReset;

use Antriver\LaravelSiteScaffolding\Users\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait PasswordResetShowTestTrait
{
    public function testShowFailsWithoutUserId()
    {
        $response = $this->sendGet(
            '/password-resets/abc'
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'userId' => ['The user id field is required.'],
            ]
        );
    }

    public function testShowFailsWithUnknownUserId()
    {
        $response = $this->sendGet(
            '/password-resets/abc',
            [
                'userId' => 123,
            ]
        );
        $this->assertResponseHasErrorType($response, ModelNotFoundException::class);
        $this->assertResponseHasError($response, 'User 123 not found.');
    }

    public function testShowFailsWithUnknownToken()
    {
        $user = $this->seedUser();

        $response = $this->sendGet(
            '/password-resets/abc',
            [
                'userId' => $user->id,
            ]
        );
        $this->assertResponseHasErrorType($response, BadRequestHttpException::class);
        $this->assertResponseHasError($response, 'This password reset link has expired.');
    }

    public function testShowSucceedsWithValidToken()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $token = uniqid();
        \DB::table('password_reset_tokens')->insert(
            [
                'userId' => $user->id,
                'token' => $token,
            ]
        );

        $response = $this->sendGet(
            '/password-resets/'.$token,
            [
                'userId' => $user->id,
            ]
        );
        $this->assertResponseOk($response);

        $this->assertResponseContains(
            $response,
            [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ],
                'resetToken' => $token,
                'resetTokenValid' => 1,
            ]
        );
    }
}
