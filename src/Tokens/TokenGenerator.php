<?php

namespace Antriver\LaravelSiteScaffolding\Tokens;

class TokenGenerator
{
    /**
     * Create a new token string.
     *
     * @return string
     */
    public function generateToken(): string
    {
        return (new \Tokenly\TokenGenerator\TokenGenerator())->generateToken(64);
    }
}
