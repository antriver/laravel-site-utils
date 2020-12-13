<?php

namespace Antriver\LaravelSiteUtils\Models\Base;

use Antriver\LaravelSiteUtils\Models\Traits\OutputsDatesTrait;
use Illuminate\Database\Eloquent\Relations\Pivot as EloquentPivot;

/**
 * Antriver\LaravelSiteUtils\Models\Base\Pivot
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Models\Base\Pivot
 *     newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Models\Base\Pivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Models\Base\Pivot query()
 * @mixin \Eloquent
 */
class Pivot extends EloquentPivot
{
    use OutputsDatesTrait;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'createdAt';

    /**
     * The name of the "delete at" column (for soft deletes).
     *
     * @var string
     */
    const DELETED_AT = 'deletedAt';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updatedAt';

    public function toArray()
    {
        $array = parent::toArray();

        $array = $this->formatArrayDates($array);

        return $array;
    }
}
