<?php

namespace Antriver\LaravelSiteScaffolding\Models\Traits;

trait BelongsToUserTrait
{
    /**
     * Return the userId this model belongs to or was created by.
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->getAttribute('userId');
    }
}
