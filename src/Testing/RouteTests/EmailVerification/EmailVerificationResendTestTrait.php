<?php

namespace Antriver\LaravelSiteUtils\Testing\RouteTests\EmailVerification;

use Antriver\LaravelSiteUtils\EmailVerification\EmailVerification;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationMail;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Faker\Generator;
use Illuminate\Support\Facades\Mail;

trait EmailVerificationResendTestTrait
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
            '/email-verifications/123/resend'
        );
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testWithNonexistentId()
    {
        $this->setCurrentUser($this->seedUser());

        $response = $this->sendPost(
            '/email-verifications/123/resend'
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

        $response = $this->sendPost(
            '/email-verifications/'.$verification->id.'/resend'
        );
        $this->assertResponseHasError(
            $response,
            'EmailVerification '.$verification->id.' not found.'
        );
    }

    public function testWithVerificationForCurrentUser()
    {
        Mail::fake();

        /** @var User $user */
        $user = $this->seedUser();

        $this->setCurrentUser($user);

        $email = $this->faker->safeEmail;
        $token = uniqid();
        $verification = new EmailVerification(
            [
                'userId' => $user->id,
                'token' => $token,
                'email' => $email,
                'type' => EmailVerification::TYPE_SIGNUP,
            ]
        );
        $this->emailVerificationRepository->persist($verification);

        $response = $this->sendPost(
            '/email-verifications/'.$verification->id.'/resend'
        );
        $this->assertResponseHasSuccess($response);

        Mail::assertSent(
            EmailVerificationMail::class,
            function (EmailVerificationMail $mail) use ($email, $token) {
                $mail->build();

                return $mail->to[0]['address'] === $email
                    && stripos($mail->actionUrl, $token) !== false;
            }
        );
    }
}
