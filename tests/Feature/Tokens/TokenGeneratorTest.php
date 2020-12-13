<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Tokens;

use Antriver\LaravelSiteUtils\Tokens\TokenGenerator;
use Antriver\LaravelSiteUtilsTests\Feature\AbstractFeatureTest;

class TokenGeneratorTest extends AbstractFeatureTest
{
    public function testGenerateToken()
    {
        $generator = new TokenGenerator();
        $token = $generator->generateToken();
        $this->assertTrue(is_string($token));
        $this->assertSame(64, strlen($token));
    }
}
