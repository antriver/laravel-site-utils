<?php

namespace Antriver\LaravelSiteScaffolding\Users;

use Tmd\LaravelRepositories\Base\AbstractCachedRepository;

/**
 * @method persist(User|UserInterface $user)
 */
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
