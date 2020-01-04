<?php

namespace Antriver\LaravelSiteUtils\Users;

interface UserPresenterInterface
{
    /**
     * @param UserInterface $user
     *
     * @return array
     */
    public function presentUser(UserInterface $user): array;
}
