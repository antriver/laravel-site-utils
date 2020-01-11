<?php

namespace Antriver\LaravelSiteScaffolding\Testing\RouteTests\Register;

use Antriver\LaravelSiteScaffolding\Users\User;
use Antriver\LaravelSiteScaffolding\Users\ValidatesUserCredentialsTrait;
use Faker\Generator;

trait RegisterStoreTestTrait
{
    use ValidatesUserCredentialsTrait;

    /**
     * @var Generator
     */
    private $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = app(Generator::class);
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
                'username' => $username
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

    public function testStoreWithValidData()
    {
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

        $this->assertResponseContains(
            [
                'user' => [
                    'username' => $username
                ]
            ]
        );
    }
}
