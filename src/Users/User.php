<?php

namespace Antriver\LaravelSiteUtils\Users;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable as AuthorizableTrait;
use Illuminate\Notifications\Notifiable;

/**
 * Antriver\LaravelSiteUtils\Users\User
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property bool $admin
 * @property bool $moderator
 * @property int $emailVerified
 * @property int $emailBounced
 * @property int|null $avatarImageId
 * @property \Illuminate\Support\Carbon $createdAt
 * @property \Illuminate\Support\Carbon|null $updatedAt
 * @property \Illuminate\Support\Carbon|null $deletedAt
 * @property string|null $deactivatedAt
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[]
 *     $notifications
 * @property-read int|null $notifications_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\Antriver\LaravelSiteUtils\Users\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereAvatarImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereDeactivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereEmailBounced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Users\User
 *     whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\Antriver\LaravelSiteUtils\Users\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Antriver\LaravelSiteUtils\Users\User withoutTrashed()
 * @mixin \Eloquent
 */
class User
    extends
    AbstractModel
    implements
    AuthorizableContract,
    AuthenticatableContract,
    CanResetPasswordContract,
    UserInterface
{
    use AuthenticatableTrait;
    use AuthorizableTrait;
    use CanResetPasswordTrait;
    use Notifiable;
    use SoftDeletes;
    use UserTrait;

    protected $casts = [
        'id' => 'int',
        'admin' => 'bool',
        'moderator' => 'bool',
        'emailBounced' => 'bool',
        'emailVerified' => 'bool',
    ];

    /**
     * Disable the 'rememberToken' field on users.
     *
     * @var bool
     */
    //protected $rememberTokenName = false;

    protected $visible = [
        'id',
        'username',
    ];

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return '';
    }

    public function toArray()
    {
        $array = parent::toArray();

        if ($this->admin) {
            $array['admin'] = true;
        }

        if ($this->moderator) {
            $array['moderator'] = true;
        }

        return $array;
    }
}
