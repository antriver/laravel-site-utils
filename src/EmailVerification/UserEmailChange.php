<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;
use Antriver\LaravelSiteScaffolding\Models\Traits\CreatedAtWithoutUpdatedAtTrait;

/**
 * Antriver\LaravelSiteScaffolding\EmailVerification\UserEmailChange
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\UserEmailChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\UserEmailChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\UserEmailChange query()
 * @mixin \Eloquent
 */
class UserEmailChange extends AbstractModel
{
    use CreatedAtWithoutUpdatedAtTrait;

    protected $table = 'user_email_changes';
}
