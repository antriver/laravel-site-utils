<?php

namespace Antriver\LaravelSiteScaffolding\Auth\Forgot;

use Antriver\LaravelSiteScaffolding\Users\User;

class ForgottenPasswordManager
{
    public function sendForgottenPasswordEmail(User $user, string $token)
    {
        \Mail::to($user->getEmail())->send(
            new ForgotDetailsMail($token, $user)
        );
    }
}
