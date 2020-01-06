<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\Auth;

use Antriver\LaravelSiteScaffolding\Users\User;
use Illuminate\Auth\AuthenticationException;

trait AuthShowTestsTrait
{
    public function testShow()
    {
        // Seed a user with a session.
        $user = factory(User::class)->create();
        $token = 'abc';
        \DB::table('user_sessions')->insert([
            'id' => $token,
            'userId' => $user->id
        ]);

        $response = $this->get('/auth?token='.$token);
        $this->assertResponseOk($response);
        $this->assertResponseContains(
            $response,
            [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ],
            ]
        );
    }

    public function testShowFailsWithoutToken()
    {
        $response = $this->get('/auth');
        $this->assertResponseHasError($response, 'Unauthenticated.');
        $this->assertResponseHasErrorType($response, AuthenticationException::class);
    }

    public function testShowFailsWithEmptyToken()
    {
        $response = $this->get('/auth?token=');
        $this->assertResponseHasError($response, 'Unauthenticated.');
        $this->assertResponseHasErrorType($response, AuthenticationException::class);
    }

    public function testShowFailsWithInvalidToken()
    {
        $response = $this->get('/auth?token=fake');
        $this->assertResponseHasError($response, 'Unauthenticated.');
        $this->assertResponseHasErrorType($response, AuthenticationException::class);
    }
}
