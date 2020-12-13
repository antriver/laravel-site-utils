<?php

namespace Antriver\LaravelSiteUtils\Testing\RouteTests\ForgotPassword;

use Antriver\LaravelSiteUtils\Auth\Forgot\ForgotDetailsMail;
use Antriver\LaravelSiteUtils\Users\User;
use Illuminate\Support\Facades\Mail;

trait ForgotPasswordStoreTestTrait
{
    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function testFailsWithNoEmail()
    {
        $response = $this->sendPost('/forgot-password');
        $this->assertResponseHasValidationError(
            $response,
            [
                'email' => ['The email field is required.'],
            ]
        );

        Mail::assertNothingSent();
    }

    public function testFailsWithUnknownEmail()
    {
        $response = $this->sendPost(
            '/forgot-password',
            [
                'email' => 'a@b.com',
            ]
        );
        $this->assertResponseHasError($response, 'There is no user with that email address.');
    }

    public function testSucceedsWithKnownEmail()
    {
        /** @var User $user */
        $user = $this->seedUser();

        $response = $this->sendPost(
            '/forgot-password',
            [
                'email' => $user->email,
            ]
        );
        $this->assertResponseHasSuccess($response);

        $tokens = \DB::select(
            'SELECT * FROM `password_reset_tokens` WHERE `userId` = ?',
            [
                $user->id,
            ]
        );
        $this->assertCount(1, $tokens);
        $this->assertNotEmpty($tokens[0]->token);

        Mail::assertSent(
            ForgotDetailsMail::class,
            function (ForgotDetailsMail $mail) use ($user, $tokens) {
                $mail->build();

                return $mail->to[0]['address'] === $user->getEmail()
                    && stripos($mail->actionUrl, $tokens[0]->token) !== false;
            }
        );
    }
}
