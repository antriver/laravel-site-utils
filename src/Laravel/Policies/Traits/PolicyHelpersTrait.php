<?php

namespace Antriver\LaravelSiteUtils\Laravel\Policies\Traits;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Antriver\LaravelSiteUtils\Models\Interfaces\BelongsToUserInterface;
use Antriver\LaravelSiteUtils\Models\User\UserInterface;

trait PolicyHelpersTrait
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
     * @param AbstractModel|BelongsToUserInterface $model
     *
     * @return bool
     */
    public function isOwner(UserInterface $user, AbstractModel $model)
    {
        return $user && $user->getKey() === $model->getUserId();
    }

    /**
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return bool
     */
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
     * @throws \Exception
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
     * @throws \Exception
     */
    public function isAdmin(UserInterface $user)
    {
        return $user->isAdmin();
    }
}
