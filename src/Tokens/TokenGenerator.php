<?php

namespace Antriver\LaravelSiteUtils\Tokens;

use Illuminate\Support\Str;

class TokenGenerator
{
    /**
     * Create a new token string.
     *
     * @return string
     */
    public function generateToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->getHashKey());
    }

    /**
     * @return string
     */
    private function getHashKey()
    {
        return config('app.key');
    }
}
