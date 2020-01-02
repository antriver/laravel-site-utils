<?php

namespace Antriver\LaravelSiteUtils\Models\Traits;

use Antriver\LaravelSiteUtils\Models\User;
use Antriver\LaravelSiteUtils\Repositories\UserRepository;

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
