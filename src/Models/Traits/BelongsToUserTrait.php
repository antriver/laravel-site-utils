<?php

namespace Antriver\SiteUtils\Models\Traits;

use Antriver\SiteUtils\Models\User;
use Antriver\SiteUtils\Repositories\UserRepository;

trait BelongsToUserTrait
{
    /**
     * Get the user this model belongs to or was created by.
     *
     * @return User
     */
    public function getUserId()
    {
        return $this->getAttribute('userId');
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->getUserRepository()->find($this->getUserId());
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return app(UserRepository::class);
    }
}
