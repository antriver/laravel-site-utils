<?php

namespace Antriver\LaravelSiteScaffolding\Bans;

use Illuminate\Database\Eloquent\Model;

interface BanPresenterInterface
{
    /**
     * @param Model|Ban $user
     *
     * @return array
     */
    public function present(Model $user): ?array;
}
