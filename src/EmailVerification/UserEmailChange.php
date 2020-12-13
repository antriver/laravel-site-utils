<?php

namespace Antriver\LaravelSiteUtils\EmailVerification;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Antriver\LaravelSiteUtils\Models\Traits\CreatedAtWithoutUpdatedAtTrait;

/**
 * Antriver\LaravelSiteUtils\EmailVerification\UserEmailChange
 *
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\UserEmailChange
 *     newModelQuery()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\UserEmailChange
 *     newQuery()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\UserEmailChange query()
 * @mixin \Eloquent
 */
class UserEmailChange extends AbstractModel
{
    use CreatedAtWithoutUpdatedAtTrait;

    protected $table = 'user_email_changes';
}
