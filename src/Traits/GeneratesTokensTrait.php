<?php

namespace Antriver\LaravelSiteUtils\Traits;

use Antriver\LaravelSiteUtils\Tokens\TokenGenerator;

trait GeneratesTokensTrait
{
    /**
     * Create a new token string.
     *
     * @return string
     */
    protected function generateToken(): string
    {
        return ((new TokenGenerator()))->generateToken();
    }
}
