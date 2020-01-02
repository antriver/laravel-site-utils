<?php

namespace Antriver\LaravelSiteUtils\Entities\User;

use Tmd\LaravelPasswordUpdater\PasswordHasher;

interface UserInterface
{
    public function getId(): int;

    public function getEmail(): ?string;

    public function setEmail(?string $email);

    public function isAdmin(): bool;

    public function isModerator(): bool;

    public function isEmailVerified(): bool;

    public function setEmailVerified(bool $emailVerified);

    public function getPossessiveUsername(): string;

    public function getUrl(): string;

    public function getUrlUsername(): string;

    public function setPassword(string $password, PasswordHasher $passwordHasher);
}
