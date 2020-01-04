<?php

namespace Antriver\LaravelSiteScaffolding\Users;

trait ValidatesUserCredentialsTrait
{
    protected function getUsernameValidationRules(UserInterface $user = null, $required = true)
    {
        $rules = [
            'max:30',
            $user ? 'unique:users,username,'.$user->getId() : 'unique:users,username',
            'regex:'.$this->getUsernameRegex(),
            'i_not_in:'.implode(',', $this->getReservedUsernames()),
        ];

        if ($required) {
            $rules[] = 'required';
        }

        return $rules;
    }

    protected function getUsernameRegex()
    {
        return '/^[A-Za-z0-9_-]{1,30}$/';
    }

    protected function getReservedUsernames()
    {
        return [
            'anonymous',
            'admin',

            // Any chat room names need to be reserved, as that would break the /messages/username+room links
            'chat',
            'modchat',
        ];
    }

    protected function getEmailValidationRules(UserInterface $user = null, $required = true)
    {
        $rules = [
            'bail',
            'email',
            //'email_valid',
            $user ? 'unique:users,email,'.$user->getId() : 'unique:users,email',
        ];

        if ($required) {
            $rules[] = 'required';
        }

        return $rules;
    }

    protected function getPasswordValidationRules($required = true)
    {
        $rules = [
            'min:3',
        ];

        if ($required) {
            $rules[] = 'required';
        }

        return $rules;
    }
}
