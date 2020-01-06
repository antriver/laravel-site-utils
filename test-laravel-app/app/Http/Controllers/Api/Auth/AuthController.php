<?php

namespace Antriver\LaravelSiteScaffolding\TestLaravelApp\Http\Controllers\Api\Auth;

use Antriver\LaravelSiteScaffolding\Auth\Http\AuthControllerTrait;
use Antriver\LaravelSiteScaffolding\TestLaravelApp\Http\Controllers\Api\AbstractApiController;

class AuthController extends AbstractApiController
{
    use AuthControllerTrait;
}
