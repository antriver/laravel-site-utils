<?php

namespace Antriver\LaravelSiteUtils\Testing\RouteTests\EmailVerification;

use Antriver\LaravelSiteUtils\EmailVerification\EmailVerification;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Faker\Generator;

trait EmailVerificationVerifyTestTrait
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
        $response = $this->sendPost(
            '/email-verifications/123/verify'
        );
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testWithNoToken()
    {
        $this->setCurrentUser($this->seedUser());

        $response = $this->sendPost(
            '/email-verifications/123/verify'
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'verificationToken' => ['The verification token field is required.'],
            ]
        );
    }

    public function testWithNonexistentId()
    {
        $this->setCurrentUser($this->seedUser());

        $response = $this->sendPost(
            '/email-verifications/123/verify',
            [
                'verificationToken' => 'abc',
            ]
        );
        $this->assertResponseHasError(
            $response,
            'EmailVerification 123 not found.'
        );
    }

    public function testWithWrongToken()
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

        $response = $this->sendPost(
            '/email-verifications/'.$verification->id.'/verify',
            [
                'verificationToken' => 'def',
            ]
        );
        $this->assertResponseHasError(
            $response,
            'Invalid token.'
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

        $response = $this->sendPost(
            '/email-verifications/'.$verification->id.'/verify',
            [
                'verificationToken' => 'abc',
            ]
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
        $this->assertNotTrue($user->emailVerified);

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

        $response = $this->sendPost(
            '/email-verifications/'.$verification->id.'/verify',
            [
                'verificationToken' => 'abc',
            ]
        );
        $this->assertResponseHasSuccess($response);
        $this->assertResponseContainsAuthInfo($response, $user);

        // Assert user is now verified.
        $user = $this->userRepository->find($user->id);
        $this->assertTrue($user->emailVerified);

        // Assert verification was deleted.
        $rows = \DB::select(
            'SELECT * FROM `email_verifications` WHERE `userId` = ?',
            [
                $user->id,
            ]
        );
        $this->assertCount(0, $rows);
    }

    public function testWithChangeVerificationForCurrentUser()
    {
        /** @var User $user */
        $user = $this->seedUser(
            [
                'email' => 'original@email.com',
                'emailVerified' => 1,
            ]
        );
        $this->assertTrue($user->emailVerified);

        $this->setCurrentUser($user);

        $verification = new EmailVerification(
            [
                'userId' => $user->id,
                'token' => 'abc',
                'email' => 'new@email.com',
                'type' => EmailVerification::TYPE_CHANGE,
            ]
        );
        $this->emailVerificationRepository->persist($verification);

        $response = $this->sendPost(
            '/email-verifications/'.$verification->id.'/verify',
            [
                'verificationToken' => 'abc',
            ]
        );
        $this->assertResponseHasSuccess($response);
        $this->assertResponseContainsAuthInfo($response, $user);

        // Assert user is still verified.
        $user = $this->userRepository->find($user->id);
        $this->assertTrue($user->emailVerified);

        // Assert user email changed.
        $this->assertSame('new@email.com', $user->email);

        // Assert verification was deleted.
        $verifications = \DB::select(
            'SELECT * FROM `email_verifications` WHERE userId = ?',
            [
                $user->id,
            ]
        );
        $this->assertCount(0, $verifications);

        // Assert change was logged.
        $emailChanges = \DB::select(
            'SELECT * FROM `user_email_changes` WHERE userId = ?',
            [
                $user->id,
            ]
        );
        $this->assertCount(1, $emailChanges);
        $this->assertSame('original@email.com', $emailChanges[0]->oldEmail);
        $this->assertSame('new@email.com', $emailChanges[0]->newEmail);
    }
}
