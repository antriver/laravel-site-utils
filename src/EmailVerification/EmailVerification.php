<?php

namespace Antriver\LaravelSiteUtils\EmailVerification;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Antriver\LaravelSiteUtils\Models\Interfaces\BelongsToUserInterface;
use Antriver\LaravelSiteUtils\Models\Traits\BelongsToUserTrait;

/**
 * @property int $id
 * @property int $userId
 * @property string $email
 * @property string $token
 * @property int|null $isChange Is this the initial verification, or changing an existing user?
 * @property string $createdAt
 * @property string|null $resentAt
 * @mixin \Eloquent
 */
class EmailVerification extends AbstractModel implements BelongsToUserInterface
{
    use BelongsToUserTrait;

    const TYPE_SIGNUP = 'signup';
    const TYPE_CHANGE = 'change';
    const TYPE_REVERIFY  = 'reverify';

    protected $table = 'email_verifications';

    protected $dates = [
        self::CREATED_AT,
        'resentAt'
    ];

    protected $visible = [
        'id',
        'email',
        'userId',
        'type',
        'createdAt',
        'resentAt'
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
