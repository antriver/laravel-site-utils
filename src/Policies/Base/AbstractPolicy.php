<?php

namespace Antriver\LaravelSiteScaffolding\Policies\Base;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;
use Antriver\LaravelSiteScaffolding\Models\Interfaces\BelongsToUserInterface;
use Antriver\LaravelSiteScaffolding\Users\User;

abstract class AbstractPolicy
{
    /**
     * Return true if the authenticated user is a level that can modify this item.
     * Moderators can edit most objects, so by default this returns if the user is a moderator.
     * Override this method to restrict access to admins.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isPrivileged(User $user)
    {
        return $user->isModerator();
    }

    /**
     * Returns true if the authenticated user is the user that created this object.
     *
     * @param User $user
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function isOwner(User $user, AbstractModel $model)
    {
        if (!$model instanceof BelongsToUserInterface) {
            return false;
        }

        return $user && $user->id === $model->getUserId();
    }

    public function isOwnerOrPrivileged(User $user, AbstractModel $model)
    {
        return $user && ($this->isOwner($user, $model) || $this->isPrivileged($user));
    }

    /**
     * Returns true if the user is a moderator.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isModerator(User $user)
    {
        return $user->isModerator();
    }

    /**
     * Returns true if the user is an admin.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isAdmin(User $user)
    {
        return $user->isAdmin();
    }
}
