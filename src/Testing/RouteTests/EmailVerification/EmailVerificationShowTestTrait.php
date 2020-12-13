<?php

namespace Antriver\LaravelSiteUtils\Testing\RouteTests\EmailVerification;

use Antriver\LaravelSiteUtils\EmailVerification\EmailVerification;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Faker\Generator;

trait EmailVerificationShowTestTrait
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var EmailVerificationRepository
     */
    private $emailVerificationRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = app(Generator::class);
        $this->emailVerificationRepository = app(EmailVerificationRepository::class);
        $this->userRepository = app(UserRepository::class);
    }

    public function testWithNoUser()
    {
        $response = $this->sendGet(
            '/email-verifications/123'
        );
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testWithNonexistentId()
    {
        $this->setCurrentUser($this->seedUser());

        $response = $this->sendGet(
            '/email-verifications/123'
        );
        $this->assertResponseHasError(
            $response,
            'EmailVerification 123 not found.'
        );
    }

    public function testWithVerificationForDifferentUser()
    {
        $this->setCurrentUser($this->seedUser());

        /** @var User $user */
        $user = $this->seedUser();

        $verification = new EmailVerification(
            [
                'userId' => $user->id,
                'token' => 'abc',
                'email' => $this->faker->safeEmail,
                'type' => EmailVerification::TYPE_SIGNUP,
            ]
        );
        $this->emailVerificationRepository->persist($verification);

        $response = $this->sendGet(
            '/email-verifications/'.$verification->id
        );
        $this->assertResponseHasError(
            $response,
            'EmailVerification '.$verification->id.' not found.'
        );
    }

    public function testWithVerificationForCurrentUser()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $this->setCurrentUser($user);

        $verification = new EmailVerification(
            [
                'userId' => $user->id,
                'token' => 'abc',
                'email' => $this->faker->safeEmail,
                'type' => EmailVerification::TYPE_SIGNUP,
            ]
        );
        $this->emailVerificationRepository->persist($verification);

        $response = $this->sendGet(
            '/email-verifications/'.$verification->id
        );
        $this->assertResponseOk($response);

        $this->assertResponseContains(
            $response,
            [
                'emailVerification' => [
                    'id' => $verification->id,
                ],
            ]
        );

        $result = $this->parseResponse($response);
        $this->assertArrayNotHasKey('token', $result['emailVerification']);
    }
}
