<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Api;

use Antriver\LaravelSiteUtils\Testing\Traits\ApiTestCaseTrait;
use Antriver\LaravelSiteUtilsTests\Feature\AbstractFeatureTest;

abstract class AbstractApiTestCase extends AbstractFeatureTest
{
    use ApiTestCaseTrait;

    protected function setUp(): void
    {
        parent::setUp();

        // The test app mounts its API routes under /api, and ApiTestCaseTrait prefixes every request with this.
        config(['app.api_url' => 'http://localhost/api']);
    }
}
