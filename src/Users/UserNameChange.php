<?php

namespace Antriver\LaravelSiteUtils\Users;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

/**
 * Amirite\Models\UserNameChange
 *
 * @property int $id
 * @property int $userId
 * @property string $oldName
 * @property string $newName
 * @property \Carbon\Carbon $createdAt
 * @mixin \Eloquent
 */
class UserNameChange extends AbstractModel
{
    protected $table = 'user_name_changes';

    protected $dates = [
        self::CREATED_AT,
    ];

    public $timestamps = false;
}
