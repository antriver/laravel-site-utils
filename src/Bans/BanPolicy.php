<?php

namespace Antriver\LaravelSiteUtils\Bans;

use Antriver\LaravelSiteUtils\Laravel\Policies\Base\AbstractPolicy;
use Antriver\LaravelSiteUtils\Laravel\Policies\Base\ModelPolicyInterface;
use Antriver\LaravelSiteUtils\Laravel\Policies\Traits\AdminOnlyPolicyTrait;

class BanPolicy extends AbstractPolicy implements ModelPolicyInterface
{
    use AdminOnlyPolicyTrait;
}
