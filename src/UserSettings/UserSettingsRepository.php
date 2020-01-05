<?php

namespace Antriver\LaravelSiteScaffolding\UserSettings;

use Antriver\LaravelSiteScaffolding\Repositories\Interfaces\UserBelongingsRepositoryInterface;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Tmd\LaravelRepositories\Base\AbstractRepository;

/**
 * @method UserSettings find(int $modelId)
 * @method UserSettings findOrFail(int $modelId)
 * @method UserSettings findOneBy(string $field, $value)
 */
class UserSettingsRepository extends AbstractRepository implements UserBelongingsRepositoryInterface
{
    /**
     * @return string
     */
    public function getModelClass()
    {
        return UserSettings::class;
    }

    /**
     * @param UserInterface $user
     *
     * @return UserSettings
     */
    public function findForUser(UserInterface $user)
    {
        return $this->find($user->id);
    }
}
