<?php

namespace Antriver\SiteUtils\Models\Ban;

interface BanInterface
{
    public function isExpired(): bool;
}
