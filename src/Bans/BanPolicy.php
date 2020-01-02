<?php

namespace Antriver\LaravelSiteUtils\Bans;

use Antriver\LaravelSiteUtils\Policies\Base\AbstractPolicy;
use Antriver\LaravelSiteUtils\Policies\Base\ModelPolicyInterface;
use Antriver\LaravelSiteUtils\Policies\Traits\AdminOnlyPolicyTrait;

class BanPolicy extends AbstractPolicy implements ModelPolicyInterface
{
    use AdminOnlyPolicyTrait;
}
