<?php

namespace Antriver\LaravelSiteScaffoldingTestApp\Http\Controllers\Api;

use Antriver\LaravelSiteScaffolding\Auth\Http\PasswordResetControllerTrait;

class ResetPasswordController extends AbstractApiController
{
    use PasswordResetControllerTrait;
}
