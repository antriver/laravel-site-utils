<?php

namespace Antriver\LaravelSiteScaffoldingTestApp;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Antriver\LaravelSiteScaffoldingTestApp\User
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property int $admin
 * @property int $moderator
 * @property int $emailVerified
 * @property int $emailBounced
 * @property int|null $avatarImageId
 * @property string $createdAt
 * @property string|null $updatedAt
 * @property string|null $deletedAt
 * @property string|null $deactivatedAt
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereAvatarImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereDeactivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereEmailBounced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteScaffoldingTestApp\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
