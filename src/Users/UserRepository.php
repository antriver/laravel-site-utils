<?php

namespace Antriver\LaravelSiteScaffolding\Users;

use Tmd\LaravelRepositories\Base\AbstractCachedRepository;

/**
 * @method persist(User $user)
 * @method find(int $userId): ?User
 * @method findOrFail(int $userId): User
 * @method findOneBy(string $field, $value): User
 */
class UserRepository extends AbstractCachedRepository
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
