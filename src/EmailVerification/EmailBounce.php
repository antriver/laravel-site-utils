<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;

/**
 * Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce
 *
 * @property int $id
 * @property string $email
 * @property int|null $userId
 * @property string $message
 * @property string $createdAt
 * @property string|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailBounce whereUserId($value)
 * @mixin \Eloquent
 */
class EmailBounce extends AbstractModel
{
    public $timestamps = false;
}
