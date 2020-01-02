<?php

namespace Antriver\LaravelSiteUtils\EmailVerification;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Antriver\LaravelSiteUtils\Models\Traits\CreatedAtWithoutUpdatedAtTrait;

/**
 * Antriver\LaravelSiteUtils\Models\UserEmailChange
 *
 * @property int $id
 * @property int $userId
 * @property string $oldEmail
 * @property string $newEmail
 * @property \Carbon\Carbon $createdAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Models\UserEmailChange
 *     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Models\UserEmailChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Models\UserEmailChange
 *     whereNewEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Models\UserEmailChange
 *     whereOldEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Models\UserEmailChange whereUserId($value)
 * @mixin \Eloquent
 */
class UserEmailChange extends AbstractModel
{
    use CreatedAtWithoutUpdatedAtTrait;

    protected $table = 'user_email_changes';
}
