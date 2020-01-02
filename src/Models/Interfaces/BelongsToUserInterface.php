<?php

namespace Antriver\LaravelSiteUtils\Models\Interfaces;

use Antriver\LaravelSiteUtils\Models\User;

interface BelongsToUserInterface
{
    /**
     * Return the userId that created this model.
     *
     * @return int|null
     */
    public function getUserId();

    public function getUser(): ?User;
}
