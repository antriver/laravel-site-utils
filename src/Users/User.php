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
 * @property int $id
 * @property string $username
 * @property string|null $email
 * @property int $emailVerified
 * @property string $password
 * @property string|null $rememberToken
 * @property int|null $avatarImageId
 * @property int $admin
 * @property int $moderator
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
    ];
}
