<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\Users;

use Antriver\LaravelSiteScaffolding\Users\UserRepository;

trait UserUpdateTestTrait
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = app(UserRepository::class);
    }

    public function testUpdateFailsIfNotLoggedIn()
    {
        $user = $this->seedUser();
        $response = $this->sendPatch(
            '/users/'.$user->id
        );
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testUpdateFailsForDifferentUser()
    {
        $user = $this->seedUser();
        $this->setCurrentUser($this->seedUser());

        $response = $this->sendPatch(
            '/users/'.$user->id
        );
        $this->assertResponseIsAuthorizationError($response);
    }

    public function testUpdateUsername()
    {
        $user = $this->seedUser();
        $this->setCurrentUser($user);

        $response = $this->sendPatch(
            '/users/'.$user->id,
            [
                'username' => 'myNewUsername'
            ]
        );
        $this->assertResponseHasSuccess($response);
    }

    public function testUpdateEmail()
    {
        $user = $this->seedUser();
        $this->setCurrentUser($user);

        $response = $this->sendPatch(
            '/users/'.$user->id,
            [
                'email' => 'my@newemail.com'
            ]
        );
        $this->assertResponseHasSuccess($response);
    }

    public function testUpdatePassword()
    {
        $user = $this->seedUser();
        $this->setCurrentUser($user);

        $response = $this->sendPatch(
            '/users/'.$user->id,
            [
                'password' => 'myNewPass'
            ]
        );
        $this->assertResponseHasSuccess($response);

        // Check password changed.
        $user = $this->userRepository->find($user->id);
        $this->assertTrue(password_verify('myNewPass', $user->password));
    }
}
