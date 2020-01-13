<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;
use Antriver\LaravelSiteScaffolding\Models\Interfaces\BelongsToUserInterface;
use Antriver\LaravelSiteScaffolding\Models\Traits\BelongsToUserTrait;

/**
 * Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *
 * @property int $id
 * @property int $userId
 * @property string $email
 * @property string $token
 * @property string $type
 * @property \Illuminate\Support\Carbon $createdAt
 * @property \Illuminate\Support\Carbon|null $resentAt
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     newModelQuery()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     newQuery()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     query()
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     whereCreatedAt($value)
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     whereEmail($value)
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     whereId($value)
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     whereResentAt($value)
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     whereToken($value)
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     whereType($value)
 * @method static
 *     \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\EmailVerification\EmailVerification
 *     whereUserId($value)
 * @mixin \Eloquent
 */
class EmailVerification extends AbstractModel implements BelongsToUserInterface
{
    use BelongsToUserTrait;

    const TYPE_SIGNUP = 'signup';

    const TYPE_CHANGE = 'change';

    const TYPE_REVERIFY = 'reverify';

    protected $table = 'email_verifications';

    protected $dates = [
        self::CREATED_AT,
        'resentAt',
    ];

    protected $visible = [
        'id',
        'email',
        'userId',
        'type',
        'createdAt',
        'resentAt',
    ];

    public $timestamps = false;

    public function getUrl()
    {
        return url('verify-email').'?id='.$this->id.'&token='.$this->token;
    }

    public function isChange(): bool
    {
        return $this->type === self::TYPE_CHANGE;
    }
}
