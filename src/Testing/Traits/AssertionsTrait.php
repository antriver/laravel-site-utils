<?php

namespace Antriver\LaravelSiteScaffolding\Testing\Traits;

use Symfony\Component\HttpFoundation\Response;

/**
 * Allows us to tweak some of Laravel's default assertion methods to our liking
 */
trait AssertionsTrait
{
    public function assertResponseStatusNot(Response $response, int $code, string $assertionMessage = null)
    {
        $assertionMessage = ($assertionMessage ?? "Response code should NOT be {$code}.")
            ." Actual code was {$response->getStatusCode()}.";

        $this->assertNotEquals($code, $response->getStatusCode(), $assertionMessage);
    }

    public function assertResponseStatus(Response $response, int $code, string $assertionMessage = null)
    {
        $assertionMessage = ($assertionMessage ?? "Response code should be {$code}.")
            ." Actual code was {$response->getStatusCode()}.";

        $this->assertEquals($code, $response->getStatusCode(), $assertionMessage);
    }

    /**
     * @param array $expectedKeys Array of keys where the key is the expected key name, and the value is a boolean to
     *                            say if it must not be null (true if it must not be null).
     * @param array $array
     * @param bool $reportExtra If true will output keys that were in $array but not in $expectedKeys.
     */
    protected function assertArrayHasKeys($expectedKeys, $array, bool $reportExtra = false)
    {
        // Check all the keys exist using array_diff_key as this allows us to fail the test and output the entire
        // list of missing keys, which is more useful. Previously assertArrayHasKey was called in a foreach loop
        // so it would fail on the first missing key and you only got the name of the first missing key.
        $missingKeys = array_diff_key($expectedKeys, $array);
        if (!empty($missingKeys)) {
            $this->fail('The following keys were missing from the array: '.implode(',', array_keys($missingKeys)));
        }

        if ($reportExtra) {
            // Check for extra keys in the $array that were not specified in $expectedKeys.
            $extraKeys = array_diff_key($array, $expectedKeys);
            if (!empty($extraKeys)) {
                echo 'Additional keys in array:'.PHP_EOL;
                print_r($extraKeys);
            }
        }

        foreach ($expectedKeys as $key => $notNull) {
            if ($notNull) {
                $this->assertNotNull(
                    $array[$key],
                    "'{$key}' must not be null."
                );
            }
        }
    }

}
