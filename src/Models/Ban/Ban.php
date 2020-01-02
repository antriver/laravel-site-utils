<?php

namespace Antriver\SiteUtils\Models\Ban;

use Antriver\SiteUtils\Models\Base\AbstractModel;

/**
 * @property int $id
 * @property int|null $userId
 * @property string|null $ip
 * @property int|null $byUserId
 * @property int|null $unbannedByUserId
 * @property string $reason
 * @property string|null $internalReason
 * @property \Carbon\Carbon $createdAt
 * @property \Carbon\Carbon|null $updatedAt
 * @property \Carbon\Carbon|null $deletedAt
 * @property \Carbon\Carbon|null $expiresAt
 * @method static \Illuminate\Database\Eloquent\Builder|Ban current()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban expired()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|Ban onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|Ban withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Ban withoutTrashed()
 * @mixin \Eloquent
 */
class Ban extends AbstractModel implements BanInterface
{
    use BanTrait;
}
