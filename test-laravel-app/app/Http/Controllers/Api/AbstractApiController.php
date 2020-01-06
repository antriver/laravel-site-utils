<?php

namespace Antriver\LaravelSiteScaffoldingTestApp\Http\Controllers\Api;

use Antriver\LaravelSiteScaffolding\Http\Controllers\ApiControllerTrait;
use Antriver\LaravelSiteScaffoldingTestApp\Http\Controllers\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    use ApiControllerTrait;
}
