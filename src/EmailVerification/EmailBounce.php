<?php

namespace Antriver\LaravelSiteUtils\EmailVerification;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

/**
 * Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *
 * @property int $id
 * @property string $email
 * @property int|null $userId
 * @property string $message
 * @property string $createdAt
 * @property string|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\EmailVerification\EmailBounce
 *     whereUserId($value)
 * @mixin \Eloquent
 */
class EmailBounce extends AbstractModel
{
    public $timestamps = false;
}
