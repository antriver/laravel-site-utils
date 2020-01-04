<?php

namespace Antriver\LaravelSiteScaffolding\Users;

use Illuminate\Database\Eloquent\Model;

interface UserPresenterInterface
{
    /**
     * @param Model|UserInterface $user
     *
     * @return array
     */
    public function present(Model $user): array;
}
