<?php

namespace Antriver\LaravelSiteUtils\Models\User;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

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
class User extends AbstractModel implements UserInterface
{
    use UserTrait;
}
