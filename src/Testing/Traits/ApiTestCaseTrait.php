<?php

namespace Antriver\LaravelSiteScaffolding\Testing\Traits;

use Antriver\LaravelSiteScaffolding\Users\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\HttpFoundation\Response;

trait ApiTestCaseTrait
{
    /**
     * The last seeded user.
     *
     * @var User|null
     */
    protected $user;

    /**
     * @return User
     */
    protected function seedUser(): User
    {
        // Seed a user with a session.
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->setApiToken(Str::random(50));
        \DB::table('user_sessions')->insert(
            [
                'id' => $user->getApiToken(),
                'userId' => $user->id,
            ]
        );

        $this->user = $user;

        return $user;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $query
     * @param array $data
     *
     * @return TestResponse
     */
    private function sendRequest(string $method, string $uri, array $query = [], array $data = []): TestResponse
    {
        if (!array_key_exists('token', $query) && $this->user) {
            $query['token'] = $this->user->getApiToken();
        }

        if (!empty($query)) {
            $uri .= '?'.http_build_query($query);
        }

        $uri = config('app.api_url').$uri;

        return $this->call($method, $uri, $data);
    }

    public function sendGet(string $uri, array $query = []): TestResponse
    {
        return $this->sendRequest('GET', $uri, $query);
    }

    public function sendPost(string $uri, array $data = [], array $query = []): TestResponse
    {
        return $this->sendRequest('POST', $uri, $query, $data);
    }

    public function sendPatch(string $uri, array $data = [], array $query = []): TestResponse
    {
        return $this->sendRequest('PATCH', $uri, $query, $data);
    }

    public function sendPut(string $uri, array $data = [], array $query = []): TestResponse
    {
        return $this->sendRequest('PUT', $uri, $query, $data);
    }

    public function sendDelete(string $uri, array $data = [], array $query = [])
    {
        return $this->sendRequest('DELETE', $uri, $query, $data);
    }

    /**
     * Because we can't change the signature of the parent methods just make it so they can't be used.
     *
     * @param $uri
     * @param array $headers
     *
     * @throws \Exception
     */
    public function get($uri, array $headers = [])
    {
        throw new \Exception('Use sendGet instead of get.');
    }

    /**
     * Because we can't change the signature of the parent methods just make it so they can't be used.
     *
     * @param $uri
     * @param array $data
     * @param array $headers
     *
     * @throws \Exception
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        throw new \Exception('Use sendPost instead of post.');
    }

    /**
     * Because we can't change the signature of the parent methods just make it so they can't be used.
     *
     * @param $uri
     * @param array $data
     * @param array $headers
     *
     * @throws \Exception
     */
    public function put($uri, array $data = [], array $headers = [])
    {
        throw new \Exception('Use sendPut instead of put.');
    }

    /**
     * Because we can't change the signature of the parent methods just make it so they can't be used.
     *
     * @param $uri
     * @param array $data
     * @param array $headers
     *
     * @throws \Exception
     */
    public function patch($uri, array $data = [], array $headers = [])
    {
        throw new \Exception('Use sendPatch instead of patch.');
    }

    /**
     * Because we can't change the signature of the parent methods just make it so they can't be used.
     *
     * @param $uri
     * @param array $data
     * @param array $headers
     *
     * @throws \Exception
     */
    public function delete($uri, array $data = [], array $headers = [])
    {
        throw new \Exception('Use sendDelete instead of delete.');
    }

    public function createUserAndLoginViaApi()
    {
        $user = factory(User::class)->create();

        /** @var TestResponse $response */
        $response = $this->post('/auth', ['username' => $user->username, 'password' => 'secret']);
        $result = $response->decodeResponseJson();

        return [
            $user,
            $result['token'],
        ];
    }

    /**
     * JSON-decode the response body and return it.
     *
     * @param TestResponse $response
     *
     * @return array
     */
    public function parseResponse(TestResponse $response): array
    {
        try {
            return \GuzzleHttp\json_decode($response->getContent(), true);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage().PHP_EOL.$response->getContent());
        }
    }

    public function assertResponseOk(TestResponse $response)
    {
        $this->printResultOnFailure(
            $response,
            function () use ($response) {
                $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
                $result = $this->parseResponse($response);
                $this->assertArrayNotHasKey('error', $result);
            }
        );
    }

    public function assertResponseNotOk(TestResponse $response)
    {
        $this->printResultOnFailure(
            $response,
            function () use ($response) {
                $this->assertNotEquals(Response::HTTP_OK, $response->getStatusCode());
            }
        );
    }

    public function assertResponseIsClientError(TestResponse $response)
    {
        $this->printResultOnFailure(
            $response,
            function () use ($response) {
                $this->assertEquals(4, substr($response->getStatusCode(), 0, 1));
            }
        );
    }

    public function assertResponseIsAuthenticationError(TestResponse $response)
    {
        $this->assertResponseHasError($response, 'Unauthenticated.');
        $this->assertResponseHasErrorType($response, AuthenticationException::class);
        $this->assertResponseHasErrorStatus($response, 403);
    }

    public function assertResponseHasError(TestResponse $response, string $error)
    {
        $this->assertResponseNotOk($response);

        $this->printResultOnFailure(
            $response,
            function () use ($response, $error) {
                $result = $this->parseResponse($response);
                $this->assertNotEmpty($result['error']);
                $this->assertEquals($error, $result['error']);
            }
        );
    }

    public function assertResponseHasErrorType(TestResponse $response, string $type)
    {
        $result = $this->parseResponse($response);

        // In case ::class was used to provide the type, only take the last part of \Type\Like\This
        $typeParts = explode('\\', $type);
        $type = end($typeParts);

        $this->printResultOnFailure(
            $response,
            function () use ($type, $result) {
                $this->assertEquals($type, $result['type']);
            }
        );
    }

    public function assertResponseHasErrorStatus(TestResponse $response, int $status)
    {
        $result = $this->parseResponse($response);

        $this->printResultOnFailure(
            $response,
            function () use ($response, $result, $status) {
                $this->assertSame($status, $response->getStatusCode());
                $this->assertSame($status, $result['status']);
            }
        );
    }

    public function assertResponseHasErrors(TestResponse $response, array $errors)
    {
        $errorStrings = [];
        foreach ($errors as $key => $keyErrors) {
            $errorStrings = array_merge($errorStrings, $keyErrors);
        }
        $errorString = implode(' ', $errorStrings);

        $this->assertResponseHasError($response, $errorString);
        $this->assertResponseIsClientError($response);

        $result = $this->parseResponse($response);

        $this->assertEquals($errorString, $result['error']);
        $this->assertEquals($result['errors'], $errors);
    }

    public function assertResponseHasValidationError(TestResponse $response, array $errors)
    {
        $this->assertResponseHasErrors($response, $errors);
        $this->assertResponseHasErrorType($response, ValidationException::class);
    }

    /**
     * @param TestResponse $response
     * @param array $expect
     *
     * @throws \Exception
     */
    public function assertResponseContains(TestResponse $response, array $expect)
    {
        $result = $this->parseResponse($response);
        try {
            $this->assertArraySubset($expect, $result);
        } catch (\Exception $e) {
            print_r($result);
            throw $e;
        }
    }

    protected function printResultOnFailure(TestResponse $response, \Closure $closure)
    {
        try {
            $closure();
        } catch (ExpectationFailedException $e) {
            print_r($response->decodeResponseJson());
            throw $e;
        }
    }
}
