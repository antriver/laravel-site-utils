<?php

namespace Antriver\LaravelSiteUtilsTestApp\Http\Controllers\Api;

use Antriver\LaravelSiteUtils\EmailVerification\Http\EmailVerificationControllerTrait;

class EmailVerificationController extends AbstractApiController
{
    use EmailVerificationControllerTrait;
}
