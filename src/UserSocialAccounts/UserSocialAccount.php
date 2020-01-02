<?php

namespace Antriver\LaravelSiteUtils\UserSocialAccounts;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

/**
 * @mixin \Eloquent
 */
class UserSocialAccount extends AbstractModel implements UserSocialAccountInterface
{
    use UserSocialAccountTrait;

    protected $table = 'user_social_accounts';

    public $timestamps = true;
}
