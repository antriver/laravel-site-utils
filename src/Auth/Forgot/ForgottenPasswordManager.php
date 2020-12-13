<?php

namespace Antriver\LaravelSiteUtils\Auth\Forgot;

use Antriver\LaravelSiteUtils\Users\User;

class ForgottenPasswordManager
{
    public function sendForgottenPasswordEmail(User $user, string $token)
    {
        \Mail::to($user->getEmail())->send(
            new ForgotDetailsMail($token, $user)
        );
    }
}
