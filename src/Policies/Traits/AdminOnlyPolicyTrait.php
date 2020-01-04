<?php

namespace Antriver\LaravelSiteScaffolding\Policies\Traits;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;

trait AdminOnlyPolicyTrait
{
    /**
     * Can the current user create a new model?
     *
     * @param UserInterface $user
     *
     * @return mixed
     */
    public function create(UserInterface $user)
    {
        return $user->isAdmin();
    }

    /**
     * Can the current user view an existing model?
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function view(UserInterface $user, AbstractModel $model)
    {
        return $user->isAdmin();
    }

    /**
     * Can the current user edit an existing model?
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function update(UserInterface $user, AbstractModel $model)
    {
        return $user->isAdmin();
    }

    /**
     * Can the current user delete an existing model?
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function destroy(UserInterface $user, AbstractModel $model)
    {
        return $user->isAdmin();
    }
}
