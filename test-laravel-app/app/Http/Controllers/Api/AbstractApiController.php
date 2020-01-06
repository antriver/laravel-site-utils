<?php

namespace Antriver\LaravelSiteScaffolding\TestLaravelApp\Http\Controllers\Api;

use Antriver\LaravelSiteScaffolding\Http\Controllers\AbstractApiControllerTrait;
use Antriver\LaravelSiteScaffolding\TestLaravelApp\Http\Controllers\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    use AbstractApiControllerTrait;
}
