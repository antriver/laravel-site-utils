<?php

namespace Antriver\LaravelSiteUtils\UserSocialAccounts;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

/**
 * Antriver\LaravelSiteUtils\UserSocialAccounts\UserSocialAccount
 *
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\UserSocialAccounts\UserSocialAccount
 *     newModelQuery()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\UserSocialAccounts\UserSocialAccount
 *     newQuery()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\UserSocialAccounts\UserSocialAccount
 *     query()
 * @mixin \Eloquent
 */
class UserSocialAccount extends AbstractModel implements UserSocialAccountInterface
{
    use UserSocialAccountTrait;

    protected $table = 'user_social_accounts';

    public $timestamps = true;
}
