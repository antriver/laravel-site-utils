<?php

namespace Antriver\LaravelSiteScaffolding\UserSettings;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;

/**
 * @property int $userId
 * @property string|null $signupIp
 * @property int|null $notificationOptions
 * @property int|null $emailNotificationOptions
 * @property int|null $pushNotificationOptions
 * @property int $queuePublishCount
 * @property string $emailKey
 * @property string|null $lastEmailedAt
 * @mixin \Eloquent
 */
class UserSettings extends AbstractModel
{
    protected $primaryKey = 'userId';

    protected $table = 'user_settings';

    public $timestamps = false;

    public function notificationEnabled($type)
    {
        return ($this->notificationOptions & $type) !== 0;
    }

    public function pushNotificationEnabled($type)
    {
        return ($this->pushNotificationOptions & $type) !== 0;
    }

    public function emailNotificationEnabled($type)
    {
        return ($this->emailNotificationOptions & $type) !== 0;
    }
}
