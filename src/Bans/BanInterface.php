<?php

namespace Antriver\LaravelSiteUtils\Bans;

interface BanInterface
{
    public function isExpired(): bool;
}
