<?php

namespace Antriver\LaravelSiteScaffolding\Repositories\Interfaces;

use Antriver\LaravelSiteScaffolding\Users\UserInterface;

interface UserBelongingsRepositoryInterface
{
    /**
     * @param UserInterface $user
     *
     * @return mixed
     */
    public function findForUser(UserInterface $user);
}
