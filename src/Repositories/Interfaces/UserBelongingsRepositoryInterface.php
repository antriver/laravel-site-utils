<?php

namespace Antriver\LaravelSiteUtils\Repositories\Interfaces;

use Antriver\LaravelSiteUtils\Users\UserInterface;

interface UserBelongingsRepositoryInterface
{
    /**
     * @param UserInterface $user
     *
     * @return mixed
     */
    public function findForUser(UserInterface $user);
}
