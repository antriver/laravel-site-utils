<?php

namespace Antriver\LaravelSiteUtilsTests\Feature;

use Antriver\LaravelSiteUtils\Testing\Traits\TestCaseTrait;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class AbstractFeatureTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    use TestCaseTrait;
}
