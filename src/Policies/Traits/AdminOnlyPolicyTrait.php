<?php

namespace Antriver\LaravelSiteScaffolding\Policies\Traits;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;
use Antriver\LaravelSiteScaffolding\Users\User;

trait AdminOnlyPolicyTrait
{
    /**
     * Can the current user create a new model?
     *
     * @param User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Can the current user view an existing model?
     *
     * @param User $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function view(User $user, AbstractModel $model)
    {
        return $user->isAdmin();
    }

    /**
     * Can the current user edit an existing model?
     *
     * @param User $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function update(User $user, AbstractModel $model)
    {
        return $user->isAdmin();
    }

    /**
     * Can the current user delete an existing model?
     *
     * @param User $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function destroy(User $user, AbstractModel $model)
    {
        return $user->isAdmin();
    }
}
