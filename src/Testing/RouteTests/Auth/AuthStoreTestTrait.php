<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\Auth;

use Antriver\LaravelSiteScaffolding\Exceptions\InvalidInputException;
use Antriver\LaravelSiteScaffolding\Users\Exceptions\UnverifiedUserException;
use Antriver\LaravelSiteScaffolding\Users\User;
use Illuminate\Foundation\Testing\TestResponse;

trait AuthStoreTestTrait
{
    public function testLoginFailsWithoutCredentials()
    {
        $response = $this->sendPost(
            '/auth',
            []
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'username' => ['The username field is required.'],
                'password' => ['The password field is required.'],
            ]
        );
    }

    public function testLoginFailsWithoutUsername()
    {
        $response = $this->sendPost(
            '/auth',
            [
                'password' => 'hello',
            ]
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'username' => ['The username field is required.'],
            ]
        );
    }

    public function testLoginFailsWithoutPassword()
    {
        $response = $this->sendPost(
            '/auth',
            [
                'username' => 'user',
            ]
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'password' => ['The password field is required.'],
            ]
        );
    }

    public function testLoginFailsWithUnknownUser()
    {
        $response = $this->sendPost(
            '/auth',
            [
                'username' => 'user',
                'password' => 'pass',
            ]
        );
        $this->assertResponseHasErrors(
            $response,
            [
                'username' => ['There is no account with that username or email.'],
            ]
        );
        $this->assertResponseHasErrorType($response, InvalidInputException::class);
    }

    public function testLoginFailsWithIncorrectPassword()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendPost(
            '/auth',
            [
                'username' => $user->username,
                'password' => 'wrong',
            ]
        );
        $this->assertResponseHasErrors(
            $response,
            [
                'password' => ['That password is not correct.'],
            ]
        );
        $this->assertResponseHasErrorType($response, InvalidInputException::class);

        $result = $this->parseResponse($response);
        $this->assertArrayNotHasKey('token', $result);
    }

    public function testLoginFailsIfVerificationRequiredAndUserNotVerified()
    {
        config(['auth.allow_unverified_user_login' => false]);
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendPost(
            '/auth',
            [
                'username' => $user->username,
                'password' => 'secret',
            ]
        );

        $this->assertResponseNotOk($response);

        $this->assertResponseHasError($response, 'This account has not yet verified their email address.');
        $this->assertResponseHasErrorType($response, UnverifiedUserException::class);

        $result = $this->parseResponse($response);
        $this->assertNotEmpty($result['jwt']);
    }

    public function testLoginSucceedsIfVerificationRequiredAndUserVerified()
    {
        config(['auth.allow_unverified_user_login' => false]);
        /** @var User $user */
        $user = $this->seedUser(
            [
                'emailVerified' => 1,
            ]
        );

        $response = $this->sendPost(
            '/auth',
            [
                'username' => $user->username,
                'password' => 'secret',
            ]
        );
        $this->assertResponseOk($response);
        $this->assertResponseContainsAuthInfo($response, $user);
    }

    public function testLoginSucceedsIfVerificationNotRequiredAndUserNotVerified()
    {
        config(['auth.allow_unverified_user_login' => true]);
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendPost(
            '/auth',
            [
                'username' => $user->username,
                'password' => 'secret',
            ]
        );
        $this->assertResponseOk($response);
        $this->assertResponseContainsAuthInfo($response, $user);
    }

    public function testLoginSucceedsIfVerificationNotRequiredAndUserVerified()
    {
        config(['auth.allow_unverified_user_login' => true]);
        /** @var User $user */
        $user = $this->seedUser(
            [
                'emailVerified' => 1,
            ]
        );

        $response = $this->sendPost(
            '/auth',
            [
                'username' => $user->username,
                'password' => 'secret',
            ]
        );
        $this->assertResponseOk($response);
        $this->assertResponseContainsAuthInfo($response, $user);
    }

    public function testLoginSucceedsWithEmail()
    {
        config(['auth.allow_unverified_user_login' => true]);
        /** @var User $user */
        $user = $this->seedUser(
            [
                'emailVerified' => 1,
            ]
        );

        $response = $this->sendPost(
            '/auth',
            [
                'username' => $user->email,
                'password' => 'secret',
            ]
        );
        $this->assertResponseOk($response);
        $this->assertResponseContainsAuthInfo($response, $user);
    }
}
