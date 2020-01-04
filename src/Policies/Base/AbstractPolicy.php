<?php

namespace Antriver\LaravelSiteScaffolding\Policies\Base;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;
use Antriver\LaravelSiteScaffolding\Models\Interfaces\BelongsToUserInterface;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;

abstract class AbstractPolicy
{
    /**
     * Return true if the authenticated user is a level that can modify this item.
     * Moderators can edit most objects, so by default this returns if the user is a moderator.
     * Override this method to restrict access to admins.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isPrivileged(UserInterface $user)
    {
        return $user->isModerator();
    }

    /**
     * Returns true if the authenticated user is the user that created this object.
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function isOwner(UserInterface $user, AbstractModel $model)
    {
        if (!$model instanceof BelongsToUserInterface) {
            return false;
        }

        return $user && $user->id === $model->getUserId();
    }

    public function isOwnerOrPrivileged(UserInterface $user, AbstractModel $model)
    {
        return $user && ($this->isOwner($user, $model) || $this->isPrivileged($user));
    }

    /**
     * Returns true if the user is a moderator.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isModerator(UserInterface $user)
    {
        return $user->isModerator();
    }

    /**
     * Returns true if the user is an admin.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isAdmin(UserInterface $user)
    {
        return $user->isAdmin();
    }
}
