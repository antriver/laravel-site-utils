<?php

namespace Antriver\LaravelSiteUtils\Models\User;

use Antriver\LaravelSiteUtils\Lang\LanguageHelpers;
use Illuminate\Support\Str;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

trait UserTrait
{
    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return !!($this->moderator || $this->admin);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return !!$this->admin;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->emailVerified;
    }

    /**
     * @return string
     */
    public function getPossessiveUsername(): string
    {
        return LanguageHelpers::possessive($this->username);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return '/user/'.$this->getUrlUsername();
    }

    /**
     * @return string
     */
    public function getUrlUsername(): string
    {
        return strtolower($this->username);
    }

    /**
     * @param string $password
     * @param PasswordHasher $passwordHasher
     */
    public function setPassword(string $password, PasswordHasher $passwordHasher)
    {
        $hash = $passwordHasher->generateHash($password);
        $this->forceFill(
            [
                'password' => $hash,
                'rememberToken' => Str::random(60),
            ]
        );
    }
}
