<?php

namespace Antriver\LaravelSiteScaffoldingTestApp\Http\Controllers\Api;

use Antriver\LaravelSiteScaffolding\Mail\Http\SnsControllerTrait;

abstract class SnsController extends AbstractApiController
{
    use SnsControllerTrait;
}
