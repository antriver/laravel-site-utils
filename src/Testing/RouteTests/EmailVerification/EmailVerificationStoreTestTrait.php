<?php

namespace Antriver\LaravelSiteUtils\Testing\RouteTests\EmailVerification;

use Antriver\LaravelSiteUtils\EmailVerification\EmailVerification;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationMail;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationRepository;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Faker\Generator;
use Illuminate\Support\Facades\Mail;

trait EmailVerificationStoreTestTrait
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
            '/email-verifications'
        );
        $this->assertResponseIsAuthenticationError($response);
    }

    public function testWithoutEmail()
    {
        $this->setCurrentUser($this->seedUser());

        $response = $this->sendPost(
            '/email-verifications'
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'email' => ['The email field is required.'],
            ]
        );
    }

    public function testWithInvalidEmail()
    {
        $this->setCurrentUser($this->seedUser());

        $response = $this->sendPost(
            '/email-verifications',
            [
                'email' => '🐈',
            ]
        );
        $this->assertResponseHasValidationError(
            $response,
            [
                'email' => ['The email must be a valid email address.'],
            ]
        );
    }

    public function testWithValidEmail()
    {
        Mail::fake();

        $this->setCurrentUser($this->seedUser());

        $email = $this->faker->safeEmail;
        $response = $this->sendPost(
            '/email-verifications',
            [
                'email' => $email,
            ]
        );
        $this->assertResponseOk($response);

        $rows = \DB::select(
            'SELECT * FROM `email_verifications` WHERE `userId` = ?',
            [
                $this->getCurrentUser()->id,
            ]
        );
        $this->assertCount(1, $rows);
        $this->assertSame(EmailVerification::TYPE_CHANGE, $rows[0]->type);
        $this->assertSame($email, $rows[0]->email);
        $this->assertNotEmpty($rows[0]->token);
        $token = $rows[0]->token;

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
