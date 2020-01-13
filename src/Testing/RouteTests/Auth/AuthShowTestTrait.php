<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\Auth;

use Antriver\LaravelSiteScaffolding\Users\User;

trait AuthShowTestTrait
{
    public function testAuthShow()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendGet('/auth', ['token' => $user->getApiToken()]);
        $this->assertResponseOk($response);
        $this->assertResponseContains(
            $response,
            [
                'token' => $user->getApiToken(),
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ],
            ]
        );
    }

    public function testAuthShowWithImplicitToken()
    {
        /** @var User $user */
        $user = $this->seedUser();
        $this->setCurrentUser($user);

        $response = $this->sendGet('/auth');
        $this->assertResponseOk($response);
        $this->assertResponseContainsAuthInfo($response, $user);
    }

    public function testAuthShowWithSeededUserAndWrongToken()
    {
        $this->seedUser();

        $response = $this->sendGet('/auth', ['token' => 'blah']);
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testAuthShowFailsWithoutToken()
    {
        $response = $this->sendGet('/auth');
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testAuthShowFailsWithEmptyToken()
    {
        $response = $this->sendGet('/auth', ['token' => '']);
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testAuthShowFailsWithInvalidToken()
    {
        $response = $this->sendGet('/auth', ['token' => 'fake']);
        $this->assertResponseIsAuthenticationError($response);
    }
}
