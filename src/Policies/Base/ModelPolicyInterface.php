<?php

namespace Antriver\LaravelSiteUtils\Policies\Base;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Antriver\LaravelSiteUtils\Users\UserInterface;

interface ModelPolicyInterface
{
    /**
     * Can the current user create a new model?
     *
     * @param UserInterface $user
     *
     * @return mixed
     */
    public function create(UserInterface $user);

    /**
     * Can the current user create a new model belonging to the specified user?
     *
     * @param UserInterface $user
     * @param UserInterface $forUser
     *
     * @return mixed
     */
    //public function createForUser(UserInterface $user, UserInterface $forUser);

    /**
     * Can the current user view an existing model?
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function view(UserInterface $user, AbstractModel $model);

    /**
     * Can the current user view existing models belonging to the specified user?
     *
     * @param UserInterface $user
     * @param UserInterface $forUser
     *
     * @return mixed
     */
    //public function viewForUser(UserInterface $user, UserInterface $forUser);

    /**
     * Can the current user edit an existing model?
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function update(UserInterface $user, AbstractModel $model);

    /**
     * Can the current user edit existing models belonging to the specified user?
     *
     * @param UserInterface $user
     * @param UserInterface $forUser
     *
     * @return mixed
     */
    //public function updateForUser(UserInterface $user, UserInterface $forUser);

    /**
     * Can the current user delete an existing model?
     *
     * @param UserInterface $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function destroy(UserInterface $user, AbstractModel $model);

    /**
     * Can the current user delete existing models belonging to the specified user?
     *
     * @param UserInterface $user
     * @param UserInterface $forUser
     *
     * @return mixed
     */
    //public function destroyForUser(UserInterface $user, UserInterface $forUser);
}
