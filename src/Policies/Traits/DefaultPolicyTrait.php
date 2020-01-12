<?php

namespace Antriver\LaravelSiteScaffolding\Policies\Traits;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;
use Antriver\LaravelSiteScaffolding\Policies\Base\AbstractPolicy;
use Antriver\LaravelSiteScaffolding\Users\User;

trait DefaultPolicyTrait
{
    public function create(User $user)
    {
        // By default, everybody can create a model.
        return true;
    }

    public function view(User $user, AbstractModel $model)
    {
        // By default, everybody can view a model.
        return true;
    }

    public function update(User $user, AbstractModel $model)
    {
        // By default, the creator or a moderator can edit a model.

        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    public function destroy(User $user, AbstractModel $model)
    {
        // By default, the creator or a moderator can delete a model.

        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    /**
     * Can the user view a model that is deleted.
     * By default only the creator or a moderator can view a model if it has been deleted.
     *
     * @param User $user
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function viewTrashed(User $user, AbstractModel $model)
    {
        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    /**
     * Can the user un-delete the model?
     * By default only a moderator can restore a model.
     *
     * @param User $user
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function restore(User $user, AbstractModel $model)
    {
        /** @var AbstractPolicy|self $this */
        return $this->isModerator($user);
    }
}
