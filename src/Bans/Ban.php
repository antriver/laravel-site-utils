<?php

namespace Antriver\LaravelSiteUtils\Bans;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;

/**
 * Antriver\LaravelSiteUtils\Bans\Ban
 *
 * @property int $id
 * @property int|null $userId
 * @property string|null $ip
 * @property int|null $byUserId
 * @property int|null $unbannedByUserId
 * @property string $reason
 * @property string|null $internalReason
 * @property \Illuminate\Support\Carbon $createdAt
 * @property \Illuminate\Support\Carbon|null $updatedAt
 * @property string|null $deletedAt
 * @property string|null $expiresAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban whereByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban
 *     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban
 *     whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban
 *     whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban
 *     whereInternalReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban
 *     whereUnbannedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban
 *     whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Bans\Ban whereUserId($value)
 * @mixin \Eloquent
 */
class Ban extends AbstractModel
{
    use BanTrait;
}
