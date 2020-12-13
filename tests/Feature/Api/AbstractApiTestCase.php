<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Api;

use Antriver\LaravelSiteUtils\Testing\Traits\ApiTestCaseTrait;
use Antriver\LaravelSiteUtilsTests\Feature\AbstractFeatureTest;

abstract class AbstractApiTestCase extends AbstractFeatureTest
{
    use ApiTestCaseTrait;
}
