<?php

namespace Antriver\LaravelSiteScaffoldingTests\Feature\Api;

use Antriver\LaravelSiteScaffolding\Testing\Traits\ApiTestCaseTrait;
use Antriver\LaravelSiteScaffoldingTests\Feature\AbstractFeatureTest;

abstract class AbstractApiTestCase extends AbstractFeatureTest
{
    use ApiTestCaseTrait;
}
