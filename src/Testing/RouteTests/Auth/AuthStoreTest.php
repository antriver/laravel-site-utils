<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\Auth;

use Antriver\LaravelSiteScaffolding\Exceptions\InvalidInputException;
use Antriver\LaravelSiteScaffolding\Testing\AbstractApiTestCase;
use Antriver\LaravelSiteScaffolding\Users\User;

abstract class AuthStoreTest extends AbstractApiTestCase
{
    public function testLoginFailsWithoutCredentials()
    {
        $response = $this->post(
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
        $response = $this->post(
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
        $response = $this->post(
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
        $response = $this->post(
            '/auth',
            [
                'username' => 'user',
                'password' => 'pass',
            ]
        );
        $this->assertResponseHasErrors(
            $response,
            [
                'username' => ['There is no account with that username.'],
            ]
        );
        $this->assertResponseHasErrorType($response, InvalidInputException::class);
    }

    public function testLoginFailsWithIncorrectPassword()
    {
        $user = factory(User::class)->create();

        $response = $this->post(
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

    public function testLoginIsSuccess()
    {
        $user = factory(User::class)->create();

        $response = $this->post(
            '/auth',
            [
                'username' => $user->username,
                'password' => 'secret',
            ]
        );

        $this->assertResponseOk($response);

        $result = $this->parseResponse($response);

        $this->assertNotEmpty($result['user']);
        $this->assertNotEmpty($result['token']);

        $this->assertNotEmpty($result['user']['id']);
        $this->assertSame($user->username, $result['user']['username']);

        // Try a request to GET /auth to check output (lazy way instead of creating new test class for that route).
        $showResponse = $this->get('/auth?token='.$result['token']);
        $this->assertResponseOk($showResponse);
        $this->assertResponseContains(
            $showResponse,
            [
                'token' => $result['token'],
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ],
            ]
        );
    }
}
