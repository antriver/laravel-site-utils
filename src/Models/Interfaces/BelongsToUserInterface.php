<?php

namespace Antriver\LaravelSiteUtils\Models\Interfaces;

interface BelongsToUserInterface
{
    /**
     * Return the userId this model belongs to or was created by.
     *
     * @return int|null
     */
    public function getUserId(): ?int;
}
