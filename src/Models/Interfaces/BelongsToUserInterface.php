<?php

namespace Antriver\SiteUtils\Models\Interfaces;

use Antriver\SiteUtils\Models\User;

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
