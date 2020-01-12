<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\PasswordReset;

use Antriver\LaravelSiteScaffolding\Users\User;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait PasswordResetStoreTestTrait
{
    public function testResetWithInvalidUser()
    {
        $response = $this->sendPatch(
            '/users/0/password'
        );
        $this->assertResponseHasError(
            $response,
            'User 0 not found.'
        );
    }

    public function testResetWithMissingToken()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendPatch(
            '/users/'.$user->id.'/password',
            [
                'password' => 'yoyo',
            ]
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'resetToken' => ['The reset token field is required.'],
            ]
        );
    }

    public function testResetWithMissingPassword()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendPatch(
            '/users/'.$user->id.'/password',
            [
                'resetToken' => 'yoyo',
            ]
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'password' => ['The password field is required.'],
            ]
        );
    }

    public function testResetWithInvalidPassword()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendPatch(
            '/users/'.$user->id.'/password',
            [
                'resetToken' => 'yoyo',
                'password' => 'a'
            ]
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'password' => ['The password must be at least 3 characters.'],
            ]
        );
    }

    public function testResetWithInvalidToken()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendPatch(
            '/users/'.$user->id.'/password',
            [
                'resetToken' => 'yoyo',
                'password' => 'abc'
            ]
        );

        $this->assertResponseHasError(
            $response,
            'This password reset link has expired.'
        );
        $this->assertResponseHasErrorType(
            $response,
            BadRequestHttpException::class
        );
    }

    public function testResetWithValidToken()
    {
        config(['auth.allow_unverified_user_login' => true]);

        /** @var User $user */
        $user = $this->seedUser();

        $token = uniqid();
        \DB::table('password_reset_tokens')->insert(
            [
                'userId' => $user->id,
                'token' => $token,
            ]
        );

        $response = $this->sendPatch(
            '/users/'.$user->id.'/password',
            [
                'resetToken' => $token,
                'password' => 'hunter2'
            ]
        );
        $this->assertResponseHasSuccess($response);

        $result = $this->parseResponse($response);
        $this->assertSame($user->username, $result['user']['username']);
        $this->assertNotEmpty($result['token']); // api key.

        $user = app(UserRepository::class)->findOrFail($user->id);

        $this->assertTrue(password_verify('hunter2', $user->password));
    }
}
