<?php

namespace Antriver\LaravelSiteUtils\Entities\UserSocialAccount;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

/**
 * Antriver\LaravelSiteUtils\Models\UserSocialAccount
 *
 * @mixin \Eloquent
 */
class UserSocialAccount extends AbstractModel
{
    protected $table = 'user_social_accounts';

    public $timestamps = true;
}
