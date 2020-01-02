<?php

namespace Antriver\LaravelSiteUtils\Entities\User;

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
