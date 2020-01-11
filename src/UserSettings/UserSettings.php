<?php

namespace Antriver\LaravelSiteScaffolding\UserSettings;

use Antriver\LaravelSiteScaffolding\Models\Base\AbstractModel;

/**
 * Antriver\LaravelSiteScaffolding\UserSettings\UserSettings
 *
 * @property int $userId
 * @property string|null $signupIp
 * @property int|null $notificationOptions
 * @property int|null $emailNotificationOptions
 * @property int|null $pushNotificationOptions
 * @property int $pushEnabled
 * @property string|null $appVersion
 * @property string|null $emailKey
 * @property string|null $lastEmailedAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings whereAppVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings whereEmailKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings whereEmailNotificationOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings whereLastEmailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings whereNotificationOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings wherePushEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings wherePushNotificationOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings whereSignupIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffolding\UserSettings\UserSettings whereUserId($value)
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
