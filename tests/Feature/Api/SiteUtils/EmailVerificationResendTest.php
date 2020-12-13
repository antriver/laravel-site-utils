<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Api\SiteUtils;

use Antriver\LaravelSiteUtilsTests\Feature\Api\AbstractApiTestCase;
use Antriver\LaravelSiteUtils\Testing\RouteTests\EmailVerification\EmailVerificationResendTestTrait;

class EmailVerificationResendTest extends AbstractApiTestCase
{
    use EmailVerificationResendTestTrait;
}
