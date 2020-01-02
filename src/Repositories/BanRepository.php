<?php

namespace Amirite\Repositories;

use Amirite\Events\ContentTextUpdatedEvent;
use Amirite\Models\Ban;
use Amirite\Models\User;
use Amirite\Repositories\Traits\ReturnsPaginatorsTrait;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Tmd\LaravelRepositories\Base\AbstractRepository;

class BanRepository extends AbstractRepository
{
    use ReturnsPaginatorsTrait;

    /**
     * Can be used to check if the given user is banned.
     *
     * @param User $user
     *
     * @return Ban|null
     */
    public function findCurrentForUser(User $user)
    {
        $result = Cache::rememberForever(
            'user-ban:'.$user->id,
            function () use ($user) {
                $ban = Ban::where('userId', $user->id)->first();

                return $ban ?: false;
            }
        );

        return $result instanceof Ban ? $result : null;
    }

    /**
     * @param User $user
     *
     * @return Ban[]
     */
    public function findFor(User $user)
    {
        return Ban::where('userId', $user->id)->orderBy('expiresAt')->orderBy('id')->get();
    }

    /**
     * @param User $user
     *
     * @return Ban[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findAllForUser(User $user)
    {
        return Ban::fromQuery(
            'SELECT * FROM bans WHERE userId = ? ORDER BY ID DESC',
            [
                $user->id
            ]
        );
    }

    /**
     * @param string $ip
     *
     * @return Ban[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findAllForIp(string $ip)
    {
        return Ban::fromQuery(
            'SELECT * FROM bans WHERE ip = ? ORDER BY ID DESC',
            [
                $ip
            ]
        );
    }

    /**
     * @param string $ip
     *
     * @return Ban|null
     */
    public function findCurrentForIp($ip)
    {
        $result = Cache::rememberForever(
            'ip-ban:'.md5($ip),
            function () use ($ip) {
                $ban = Ban::where('ip', $ip)->first();

                return $ban ?: false;
            }
        );

        return $result instanceof Ban ? $result : null;
    }

    public function allCurrent($page = 1)
    {
        /** @var Builder $query */
        $query = Ban::orderBy('id', 'DESC');

        return $this->getLengthAwarePaginatorFromBuilder($query, $page);
    }

    public function allUnbanned($page = 1)
    {
        $query = Ban::onlyTrashed()->orderBy('deletedAt', 'DESC');

        return $this->getLengthAwarePaginatorFromBuilder($query, $page);
    }

    /**
     * Returns all bans that have expired but are not yet deleted.
     * Used in a cron command to automatically delete.
     *
     * @return Ban[]|Collection
     */
    public function getExpiredBans()
    {
        return Ban::expired()->get();
    }

    /**
     * Called when the model is inserted, updated, or deleted.
     * (AFTER the onInsert/onUpdate/onDelete methods are called.)
     *
     * @param EloquentModel|Ban $model
     * @param array $dirtyAttributes
     */
    protected function onChange(EloquentModel $model, array $dirtyAttributes = null)
    {
        if ($model->userId) {
            Cache::forget('user-ban:'.$model->userId);
        }

        if ($model->ip) {
            Cache::forget('ip-ban:'.md5($model->ip));
        }

        event(new ContentTextUpdatedEvent($model));
    }

    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return EloquentModel|string|\Illuminate\Database\Eloquent\Builder
     */
    public function getModelClass()
    {
        return Ban::class;
    }
}
