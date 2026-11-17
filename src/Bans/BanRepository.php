<?php

namespace Antriver\LaravelSiteUtils\Bans;

//use Antriver\LaravelSiteUtils\Events\ContentTextUpdatedEvent;
use Antriver\LaravelSiteUtils\Pagination\ReturnsPaginatorsTrait;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Antriver\LaravelRepositories\Base\AbstractRepository;

class BanRepository extends AbstractRepository
{
    use ReturnsPaginatorsTrait;

    /**
     * Can be used to check if the given user is banned.
     *
     * @param UserInterface $user
     *
     * @return Ban|null
     */
    public function findCurrentForUser(UserInterface $user): ?Ban
    {
        return $this->rememberBan(
            $this->getUserCacheKey($user->getId()),
            fn () => Ban::where('userId', $user->getId())->first()
        );
    }

    /**
     * @param UserInterface $user
     *
     * @return Ban[]
     */
    public function findFor(UserInterface $user)
    {
        return Ban::where('userId', $user->getId())->orderBy('expiresAt')->orderBy('id')->get();
    }

    /**
     * @param UserInterface $user
     *
     * @return Ban[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findAllForUser(UserInterface $user)
    {
        return Ban::fromQuery(
            'SELECT * FROM `bans` WHERE `userId` = ? ORDER BY `ID` DESC',
            [
                $user->getId(),
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
            'SELECT * FROM `bans` WHERE `ip` = ? ORDER BY `ID` DESC',
            [
                $ip,
            ]
        );
    }

    /**
     * @param string $ip
     *
     * @return Ban|null
     */
    public function findCurrentForIp(string $ip)
    {
        return $this->rememberBan(
            $this->getIpCacheKey($ip),
            fn () => Ban::where('ip', $ip)->first()
        );
    }

    public function allCurrent(int $page = 1)
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
    protected function onChange(EloquentModel $model, ?array $dirtyAttributes = null)
    {
        if ($model->userId) {
            Cache::forget($this->getUserCacheKey($model->userId));
        }

        if ($model->ip) {
            Cache::forget($this->getIpCacheKey($model->ip));
        }

        //event(new ContentTextUpdatedEvent($model));
    }

    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return EloquentModel|string|\Illuminate\Database\Eloquent\Builder
     */
    public function getModelClass(): string
    {
        return Ban::class;
    }

    /**
     * Returns the ban found by $query, caching it forever under $key.
     * The ban is cached as an array of attributes rather than a Ban object, so it still works when the cache is
     * not allowed to unserialize objects (Laravel's cache.serializable_classes option).
     */
    private function rememberBan(string $key, callable $query): ?Ban
    {
        $attributes = Cache::rememberForever(
            $key,
            function () use ($query) {
                $ban = $query();

                return $ban ? $ban->getAttributes() : false;
            }
        );

        return is_array($attributes) ? (new Ban())->newFromBuilder($attributes) : null;
    }

    /**
     * Returns the cache key for the current ban of a user.
     * The v2 suffix keeps entries cached as Ban objects by older versions from being read.
     */
    private function getUserCacheKey(int $userId): string
    {
        return 'user-ban-v2:'.$userId;
    }

    /**
     * Returns the cache key for the current ban of an IP address.
     */
    private function getIpCacheKey(string $ip): string
    {
        return 'ip-ban-v2:'.md5($ip);
    }
}
