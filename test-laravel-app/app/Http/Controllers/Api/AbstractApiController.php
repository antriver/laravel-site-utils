<?php

namespace Antriver\LaravelSiteUtilsTestApp\Http\Controllers\Api;

use Antriver\LaravelSiteUtils\Http\Controllers\ApiControllerTrait;
use Antriver\LaravelSiteUtilsTestApp\Http\Controllers\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    use ApiControllerTrait;
}
