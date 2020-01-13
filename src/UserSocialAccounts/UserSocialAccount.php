<?php

namespace Antriver\LaravelSiteScaffolding\UserSocialAccounts;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;

/**
 * Antriver\LaravelSiteScaffolding\UserSocialAccounts\UserSocialAccount
 *
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSocialAccounts\UserSocialAccount
 *     newModelQuery()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSocialAccounts\UserSocialAccount
 *     newQuery()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSocialAccounts\UserSocialAccount
 *     query()
 * @mixin \Eloquent
 */
class UserSocialAccount extends AbstractModel implements UserSocialAccountInterface
{
    use UserSocialAccountTrait;

    protected $table = 'user_social_accounts';

    public $timestamps = true;
}
