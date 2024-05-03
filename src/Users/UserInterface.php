<?php

namespace Antriver\LaravelSiteUtils\Users;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserInterface extends Authenticatable
{
    public function getId(): int;

    public function getEmail(): ?string;

    public function setEmail(?string $email);

    public function isAdmin(): bool;

    public function isModerator(): bool;

    public function isDeactivated(): bool;

    public function isEmailVerified(): bool;

    public function setEmailVerified(bool $emailVerified);

    public function hasEmailBounced(): bool;

    public function setEmailBounced(bool $emailBounced);

    public function getPossessiveUsername(): string;

    public function getUrl(): string;

    public function getUrlUsername(): string;

    public function setPassword(string $password, PasswordHasher $passwordHasher);
}
