<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\Register;

use Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerificationMail;
use Antriver\LaravelSiteScaffolding\Users\User;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Antriver\LaravelSiteScaffolding\Users\ValidatesUserCredentialsTrait;
use Faker\Generator;

trait RegisterStoreTestTrait
{
    use ValidatesUserCredentialsTrait;

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

    public function testStoreWithoutData()
    {
        $response = $this->sendPost('/users');

        $this->assertResponseHasValidationError(
            $response,
            [
                'username' => [
                    'The username field is required.',
                ],
                'email' => [
                    'The email field is required.',
                ],
                'password' => [
                    'The password field is required.',
                ],
            ]
        );
    }

    public function testStoreWithInvalidUsername()
    {
        $response = $this->sendPost(
            '/users',
            [
                'username' => '!',
                'email' => $this->faker->email,
                'password' => $this->faker->password,
            ]
        );

        $this->assertResponseHasValidationError(
            $response,
            [
                'username' => [
                    'The username format is invalid.',
                ],
            ]
        );
    }

    public function testStoreWithDuplicateUsername()
    {
        $username = $this->faker->regexify($this->getUsernameRegex());
        factory(User::class)->create(
            [
                'username' => $username,
            ]
        );

        $response = $this->sendPost(
            '/users',
            [
                'username' => $username,
                'email' => $this->faker->email,
                'password' => $this->faker->password,
            ]
        );

        $this->assertResponseHasValidationError(
            $response,
            [
                'username' => [
                    'The username has already been taken.',
                ],
            ]
        );
    }

    public function testStoreWithInvalidEmail()
    {
        $response = $this->sendPost(
            '/users',
            [
                'username' => $this->faker->regexify($this->getUsernameRegex()),
                'email' => 'a',
                'password' => $this->faker->password,
            ]
        );

        $this->assertResponseHasValidationError(
            $response,
            [
                'email' => [
                    'The email must be a valid email address.',
                ],
            ]
        );
    }

    public function testStoreWithDuplicateEmail()
    {
        $email = $this->faker->email;
        factory(User::class)->create(
            [
                'email' => $email,
            ]
        );

        $response = $this->sendPost(
            '/users',
            [
                'username' => $this->faker->regexify($this->getUsernameRegex()),
                'email' => $email,
                'password' => $this->faker->password,
            ]
        );

        $this->assertResponseHasValidationError(
            $response,
            [
                'email' => [
                    'The email has already been taken.',
                ],
            ]
        );
    }

    public function testStoreWithInvalidPassword()
    {
        $response = $this->sendPost(
            '/users',
            [
                'username' => $this->faker->regexify($this->getUsernameRegex()),
                'email' => $this->faker->email,
                'password' => 'a',
            ]
        );

        $this->assertResponseHasValidationError(
            $response,
            [
                'password' => [
                    'The password must be at least 3 characters.',
                ],
            ]
        );
    }

    public function dataForTestStoreWithValidData()
    {
        return [
            'Send verification and no unverified login' => [
                'config' => [
                    'app.send_email_verification_on_signup' => true,
                    'auth.allow_unverified_user_login' => false,
                ],
                'assertMailSent' => true,
                'assertAuthResponse' => false,
            ],

            'No verification sent and no unverified login' => [
                'config' => [
                    'app.send_email_verification_on_signup' => false,
                    'auth.allow_unverified_user_login' => false,
                ],
                'assertMailSent' => false,
                'assertAuthResponse' => false,
            ],

            'Send verification and unverified login' => [
                'config' => [
                    'app.send_email_verification_on_signup' => true,
                    'auth.allow_unverified_user_login' => true,
                ],
                'assertMailSent' => true,
                'assertAuthResponse' => true,
            ],

            'No verification sent and unverified login' => [
                'config' => [
                    'app.send_email_verification_on_signup' => false,
                    'auth.allow_unverified_user_login' => true,
                ],
                'assertMailSent' => false,
                'assertAuthResponse' => true,
            ],
        ];
    }

    /**
     * @dataProvider dataForTestStoreWithValidData
     *
     * @param bool $assertMailSent
     * @param bool $assertAuthResponse
     */
    public function testStoreWithValidData(array $config, bool $assertMailSent, bool $assertAuthResponse)
    {
        config($config);

        \Mail::fake();

        $username = $this->faker->regexify($this->getUsernameRegex());
        $email = $this->faker->email;
        $password = $this->faker->password;

        $response = $this->sendPost(
            '/users',
            [
                'username' => $username,
                'email' => $email,
                'password' => $password,
            ]
        );
        $this->assertResponseOk($response);

        $result = $this->parseResponse($response);
        $user = $this->userRepository->findOrFail($result['user']['id']);
        $this->assertSame($email, $user->email);

        $this->assertResponseContains(
            $response,
            [
                'user' => [
                    'username' => $username,
                ],
            ]
        );

        if ($assertMailSent) {
            \Mail::assertSent(
                EmailVerificationMail::class,
                function (EmailVerificationMail $mail) use ($user) {
                    return $mail->to[0]['address'] === $user->email;
                }
            );
        } else {
            \Mail::assertNothingSent();
        }

        if ($assertAuthResponse) {
            $this->assertResponseContainsAuthInfo($response, $user);
        } else {
            $this->assertArrayNotHasKey('token', $result);
        }
    }
}
