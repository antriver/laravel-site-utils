<?php

namespace Antriver\LaravelSiteScaffolding\Traits;

use Antriver\LaravelSiteScaffolding\Tokens\TokenGenerator;

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
