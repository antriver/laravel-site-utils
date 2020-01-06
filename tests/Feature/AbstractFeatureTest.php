<?php

namespace Antriver\LaravelSiteScaffoldingTests\Feature;

use Antriver\LaravelSiteScaffolding\Testing\Traits\TestCaseTrait;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class AbstractFeatureTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    use TestCaseTrait;
}
