<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\EmailVerification;

use Antriver\LaravelSiteScaffolding\Users\User;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Illuminate\Auth\AuthenticationException;

trait EmailVerificationIndexTestTrait
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

    public function testFailsWithoutToken()
    {
        $response = $this->sendGet('/email-verifications');
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testIndexWithNoResults()
    {
        $this->seedUser();

        $response = $this->sendGet('/email-verifications');
        $this->assertResponseOk($response);

        $result = $this->parseResponse($response);
        $this->assertFalse($result['isVerified']);
        $this->assertNull($result['emailVerification']);
    }

    public function testIndexWithVerification()
    {
        /** @var User $user */
        $user = $this->seedUser();
        $user->setEmailVerified(true);
        $this->userRepository->persist($user);

        $response = $this->sendGet('/email-verifications');
        $this->assertResponseOk($response);

        $result = $this->parseResponse($response);
        $this->assertTrue($result['isVerified']);
        $this->assertNull($result['emailVerification']);
    }
}
