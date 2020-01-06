<?php

namespace Antriver\LaravelSiteScaffolding\Users;

use Antriver\LaravelSiteScaffolding\Lang\LanguageHelpers;
use Illuminate\Support\Str;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

trait UserTrait
{
    /**
     * @var string|null
     */
    private $apiToken;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email)
    {
        $this->email = $email;
    }

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

    public function isDeactivated(): bool
    {
        return !empty($this->deactivatedAt);
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(bool $emailVerified)
    {
        $this->emailVerified = $emailVerified;
    }

    /**
     * @return bool
     */
    public function hasEmailBounced(): bool
    {
        return $this->emailBounced;
    }

    public function setEmailBounced(bool $emailBounced)
    {
        $this->emailBounced = $emailBounced;
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

    /**
     * @return string
     */
    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    /**
     * @param string $apiToken
     */
    public function setApiToken(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }
}
