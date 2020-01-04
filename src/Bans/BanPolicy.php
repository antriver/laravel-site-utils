<?php

namespace Antriver\LaravelSiteScaffolding\Bans;

use Antriver\LaravelSiteScaffolding\Policies\Base\AbstractPolicy;
use Antriver\LaravelSiteScaffolding\Policies\Base\ModelPolicyInterface;
use Antriver\LaravelSiteScaffolding\Policies\Traits\AdminOnlyPolicyTrait;

class BanPolicy extends AbstractPolicy implements ModelPolicyInterface
{
    use AdminOnlyPolicyTrait;
}
