<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;

/**
 * @property int $id
 * @property string $email
 * @property int|null $userId
 * @property string $message
 * @property string $createdAt
 * @property string|null $type
 * @mixin \Eloquent
 */
class EmailBounce extends AbstractModel
{
    public $timestamps = false;
}
