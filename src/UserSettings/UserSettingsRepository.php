<?php

namespace Antriver\LaravelSiteUtils\UserSettings;

use Antriver\LaravelSiteUtils\Repositories\Interfaces\UserBelongingsRepositoryInterface;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Antriver\LaravelRepositories\Base\AbstractRepository;

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
