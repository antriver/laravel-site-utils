<?php

namespace Antriver\LaravelSiteScaffoldingTests\Feature\Api\Scaffolding;

use Antriver\LaravelSiteScaffoldingTests\Feature\Api\AbstractApiTestCase;
use Antriver\LaravelSiteScaffolding\Testing\RouteTests\EmailVerification\EmailVerificationResendTestTrait;

class EmailVerificationResendTest extends AbstractApiTestCase
{
    use EmailVerificationResendTestTrait;
}
