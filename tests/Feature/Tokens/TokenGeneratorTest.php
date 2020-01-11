<?php

namespace Antriver\LaravelSiteScaffoldingTests\Feature\Tokens;

use Antriver\LaravelSiteScaffolding\Tokens\TokenGenerator;
use Antriver\LaravelSiteScaffoldingTests\Feature\AbstractFeatureTest;

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
