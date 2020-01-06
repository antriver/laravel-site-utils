<?php

namespace Antriver\LaravelSiteScaffoldingTests\Feature;

use Antriver\LaravelSiteScaffolding\Testing\Traits\ApiTestCaseTrait;

abstract class AbstractApiTestCase extends AbstractFeatureTest
{
    use ApiTestCaseTrait;
}
