<?php

namespace Antriver\LaravelSiteUtils\Users;

use Antriver\LaravelSiteUtils\Policies\Base\AbstractPolicy;

class UserPolicy extends AbstractPolicy
{
    /**
     * Check if the logged in user is the same as the target used.
     *
     * @param User $actioningUser
     * @param User $user
     *
     * @return bool
     */
    private function isSelf(User $actioningUser, User $user)
    {
        return $actioningUser->id === $user->id;
    }

    /**
     * Determine if the given comment can be updated by the user.
     *
     * @param User $actioningUser
     * @param User $user
     *
     * @return bool
     *
     */
    public function update(User $actioningUser, User $user)
    {
        return $this->isSelf($actioningUser, $user) || $actioningUser->isAdmin();
    }

    public function destroy(User $actioningUser, User $user)
    {
        return $actioningUser->isAdmin();
    }
}
