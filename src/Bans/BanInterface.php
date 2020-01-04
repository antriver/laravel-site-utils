<?php

namespace Antriver\LaravelSiteScaffolding\Bans;

interface BanInterface
{
    public function isExpired(): bool;
}
