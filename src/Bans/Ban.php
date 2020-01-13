<?php

namespace Antriver\LaravelSiteScaffolding\Bans;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;

/**
 * Antriver\LaravelSiteScaffolding\Bans\Ban
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
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban whereByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban
 *     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban
 *     whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban
 *     whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban
 *     whereInternalReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban
 *     whereUnbannedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban
 *     whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\Bans\Ban whereUserId($value)
 * @mixin \Eloquent
 */
class Ban extends AbstractModel implements BanInterface
{
    use BanTrait;
}
