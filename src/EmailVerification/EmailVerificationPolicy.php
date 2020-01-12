<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification;

use Antriver\LaravelSiteScaffolding\Policies\Base\AbstractPolicy;
use Antriver\LaravelSiteScaffolding\Users\User;

class EmailVerificationPolicy extends AbstractPolicy
{
    public function isPrivileged(User $user)
    {
        return $user->isAdmin();
    }

    public function resend(User $user, EmailVerification $model)
    {
        return $this->isOwnerOrPrivileged($user, $model);
    }

    public function destroy(User $user, EmailVerification $model)
    {
        return $this->isOwnerOrPrivileged($user, $model);
    }
}
