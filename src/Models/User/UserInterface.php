<?php

namespace Antriver\LaravelSiteUtils\Models\User;

use Tmd\LaravelPasswordUpdater\PasswordHasher;

interface UserInterface
{
    public function getKey();

    public function isAdmin(): bool;

    public function isModerator(): bool;

    public function isVerified(): bool;

    public function getPossessiveUsername(): string;

    public function getUrl(): string;

    public function getUrlUsername(): string;

    public function setPassword(string $password, PasswordHasher $passwordHasher);
}
