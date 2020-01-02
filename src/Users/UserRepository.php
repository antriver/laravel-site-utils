<?php

namespace Antriver\LaravelSiteUtils\Users;

use Antriver\LaravelSiteUtils\Users\UserRepositoryInterface;
use Tmd\LaravelRepositories\Base\AbstractCachedRepository;

class UserRepository extends AbstractCachedRepository implements UserRepositoryInterface
{
    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return string
     */
    public function getModelClass()
    {
        return User::class;
    }
}