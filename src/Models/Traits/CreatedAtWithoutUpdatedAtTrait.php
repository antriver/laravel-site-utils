<?php

namespace Antriver\LaravelSiteUtils\Models\Traits;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

/**
 * Class CreatedAtWithoutUpdatedAtTrait
 *
 * Allows a model to have only a createdAt column with no updatedAt
 */
trait CreatedAtWithoutUpdatedAtTrait
{
    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        /** @var AbstractModel $this */

        return $this->usesTimestamps() ? [$this->getCreatedAtColumn()] : [];
    }

    public function setUpdatedAt($value)
    {
        return $this;
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }
}
