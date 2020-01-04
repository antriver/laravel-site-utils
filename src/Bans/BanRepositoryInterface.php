<?php

namespace Antriver\LaravelSiteScaffolding\Bans;

use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Tmd\LaravelRepositories\Interfaces\RepositoryInterface;

interface BanRepositoryInterface extends RepositoryInterface
{
    public function findCurrentForUser(UserInterface $user): ?Ban;
}
