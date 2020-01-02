<?php

namespace Antriver\SiteUtils\Models;

use Antriver\SiteUtils\Models\Base\AbstractModel;

/**
 * Antriver\SiteUtils\Models\UserSocialAccount
 *
 * @mixin \Eloquent
 */
class UserSocialAccount extends AbstractModel
{
    protected $table = 'user_social_accounts';

    public $timestamps = true;
}
