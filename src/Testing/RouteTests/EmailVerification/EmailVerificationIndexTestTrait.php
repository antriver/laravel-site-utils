<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\EmailVerification;

use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification;
use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteScaffolding\Users\User;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Faker\Generator;

trait EmailVerificationIndexTestTrait
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = app(Generator::class);
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

    public function testIndexVerified()
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

    public function testIndexWithVerification()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $repo = app(EmailVerificationRepository::class);
        $verification = new EmailVerification(
            [
                'userId' => $user->id,
                'token' => 'abc',
                'email' => $this->faker->safeEmail,
                'type' => EmailVerification::TYPE_SIGNUP,
            ]
        );
        $repo->persist($verification);

        $response = $this->sendGet('/email-verifications');
        $this->assertResponseOk($response);

        $this->assertResponseContains(
            $response,
            [
                'isVerified' => false,
                'emailVerification' => [
                    'id' => $verification->id,
                    'userId' => $user->id,
                    'email' => $verification->email,
                    'type' => EmailVerification::TYPE_SIGNUP,
                ],
            ]
        );

        $result = $this->parseResponse($response);
        $this->assertArrayNotHasKey('token', $result['emailVerification']);
    }

    public function testIndexWithMultipleVerifications()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $repo = app(EmailVerificationRepository::class);
        $verification1 = new EmailVerification(
            [
                'userId' => $user->id,
                'token' => 'abc',
                'email' => $this->faker->safeEmail,
                'type' => EmailVerification::TYPE_SIGNUP,
            ]
        );
        $repo->persist($verification1);

        $verification2 = new EmailVerification(
            [
                'userId' => $user->id,
                'token' => 'def',
                'email' => $this->faker->safeEmail,
                'type' => EmailVerification::TYPE_SIGNUP,
            ]
        );
        $repo->persist($verification2);

        $response = $this->sendGet('/email-verifications');
        $this->assertResponseOk($response);

        $this->assertResponseContains(
            $response,
            [
                'emailVerification' => [
                    'id' => $verification2->id,
                    'email' => $verification2->email,
                    'userId' => $verification2->userId,
                    'type' => EmailVerification::TYPE_SIGNUP,
                ],
            ]
        );

        $result = $this->parseResponse($response);
        $this->assertArrayNotHasKey('token', $result['emailVerification']);
    }
}
