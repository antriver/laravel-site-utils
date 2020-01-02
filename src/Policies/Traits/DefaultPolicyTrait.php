<?php

namespace Antriver\LaravelSiteUtils\Policies\Traits;

use Antriver\LaravelSiteUtils\Policies\Base\AbstractPolicy;
use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Antriver\LaravelSiteUtils\Users\UserInterface;

trait DefaultPolicyTrait
{
    public function create(UserInterface $user)
    {
        // By default, everybody can create a model.
        return true;
    }

    public function view(UserInterface $user, AbstractModel $model)
    {
        // By default, everybody can view a model.
        return true;
    }

    public function update(UserInterface $user, AbstractModel $model)
    {
        // By default, the creator or a moderator can edit a model.

        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    public function destroy(UserInterface $user, AbstractModel $model)
    {
        // By default, the creator or a moderator can delete a model.

        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    /**
     * Can the user view a model that is deleted.
     * By default only the creator or a moderator can view a model if it has been deleted.
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function viewTrashed(UserInterface $user, AbstractModel $model)
    {
        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    /**
     * Can the user un-delete the model?
     * By default only a moderator can restore a model.
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function restore(UserInterface $user, AbstractModel $model)
    {
        /** @var AbstractPolicy|self $this */
        return $this->isModerator($user);
    }
}
