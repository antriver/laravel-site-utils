<?php

namespace Antriver\LaravelSiteScaffolding\Testing;

use Antriver\LaravelSiteScaffolding\Users\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiTestCase extends AbstractTestCase
{
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $url = config('app.api_url').$uri;

        return parent::call($method, $url, $parameters, $cookies, $files, $server, $content);
    }

    protected function createUserAndLoginViaApi()
    {
        $user = factory(User::class)->create();

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
    protected function parseResponse(TestResponse $response): array
    {
        try {
            return \GuzzleHttp\json_decode($response->getContent(), true);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage().PHP_EOL.$response->getContent());
        }
    }

    protected function assertResponseOk(TestResponse $response)
    {
        $this->printResultOnFailure(
            $response,
            function () use ($response) {
                $this->assertResponseStatus($response->baseResponse, Response::HTTP_OK);
                $result = $this->parseResponse($response);
                $this->assertArrayNotHasKey('error', $result);
            }
        );
    }

    protected function assertResponseNotOk(TestResponse $response)
    {
        $this->printResultOnFailure(
            $response,
            function () use ($response) {
                $this->assertResponseStatusNot($response->baseResponse, Response::HTTP_OK);
            }
        );
    }

    protected function assertResponseIsClientError(TestResponse $response)
    {
        $this->printResultOnFailure(
            $response,
            function () use ($response) {
                $this->assertEquals(4, substr($response->getStatusCode(), 0, 1));
            }
        );
    }

    protected function assertResponseHasError(TestResponse $response, string $error)
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

    protected function assertResponseHasErrorType(TestResponse $response, string $type)
    {
        $result = $this->parseResponse($response);

        // In case ::class was used to provide the type, only take the last part of \Type\Like\This
        $typeParts = explode('\\', $type);
        $type = end($typeParts);

        $this->assertEquals($type, $result['type']);
    }

    protected function assertResponseHasErrors(TestResponse $response, array $errors)
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

    protected function assertResponseHasValidationError(TestResponse $response, array $errors)
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
    protected function assertResponseContains(TestResponse $response, array $expect)
    {
        $result = $this->parseResponse($response);
        try {
            $this->assertArraySubset($expect, $result);
        } catch (\Exception $e) {
            print_r($result);
            throw $e;
        }
    }

    private function printResultOnFailure(TestResponse $response, \Closure $closure)
    {
        try {
            $closure();
        } catch (ExpectationFailedException $e) {
            print_r($response->decodeResponseJson());
            throw $e;
        }
    }
}
