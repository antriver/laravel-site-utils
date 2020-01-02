<?php

namespace Antriver\LaravelSiteUtils\Models\Ban;

interface BanInterface
{
    public function isExpired(): bool;
}
