<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;
use Antriver\LaravelSiteScaffolding\Models\Traits\CreatedAtWithoutUpdatedAtTrait;

/**
 * Antriver\LaravelSiteScaffolding\Models\UserEmailChange
 *
 * @property int $id
 * @property int $userId
 * @property string $oldEmail
 * @property string $newEmail
 * @property \Carbon\Carbon $createdAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Models\UserEmailChange
 *     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Models\UserEmailChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Models\UserEmailChange
 *     whereNewEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Models\UserEmailChange
 *     whereOldEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Models\UserEmailChange whereUserId($value)
 * @mixin \Eloquent
 */
class UserEmailChange extends AbstractModel
{
    use CreatedAtWithoutUpdatedAtTrait;

    protected $table = 'user_email_changes';
}
