<?php

namespace Antriver\LaravelSiteUtils\Bans;

use Antriver\LaravelSiteUtils\Users\UserInterface;
use Tmd\LaravelRepositories\Interfaces\RepositoryInterface;

interface BanRepositoryInterface extends RepositoryInterface
{
    public function findCurrentForUser(UserInterface $user): ?Ban;
}
