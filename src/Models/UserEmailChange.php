<?php

namespace Antriver\SiteUtils\Models;

use Antriver\SiteUtils\Models\Base\AbstractModel;
use Antriver\SiteUtils\Models\Traits\CreatedAtWithoutUpdatedAtTrait;

/**
 * Antriver\SiteUtils\Models\UserEmailChange
 *
 * @property int $id
 * @property int $userId
 * @property string $oldEmail
 * @property string $newEmail
 * @property \Carbon\Carbon $createdAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\SiteUtils\Models\UserEmailChange
 *     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\SiteUtils\Models\UserEmailChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\SiteUtils\Models\UserEmailChange
 *     whereNewEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\SiteUtils\Models\UserEmailChange
 *     whereOldEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\SiteUtils\Models\UserEmailChange whereUserId($value)
 * @mixin \Eloquent
 */
class UserEmailChange extends AbstractModel
{
    use CreatedAtWithoutUpdatedAtTrait;

    protected $table = 'user_email_changes';
}
