<?php

namespace Antriver\LaravelSiteUtils\Bans;

//use Amirite\Events\ContentTextUpdatedEvent;
use Antriver\LaravelSiteUtils\Pagination\ReturnsPaginatorsTrait;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Tmd\LaravelRepositories\Base\AbstractRepository;

class BanRepository extends AbstractRepository implements BanRepositoryInterface
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
        $result = Cache::rememberForever(
            'user-ban:'.$user->getId(),
            function () use ($user) {
                $ban = Ban::where('userId', $user->getId())->first();

                return $ban ?: false;
            }
        );

        return $result instanceof Ban ? $result : null;
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
            'SELECT * FROM bans WHERE userId = ? ORDER BY ID DESC',
            [
                $user->getId()
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
    public function findCurrentForIp(string $ip)
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
    protected function onChange(EloquentModel $model, array $dirtyAttributes = null)
    {
        if ($model->userId) {
            Cache::forget('user-ban:'.$model->userId);
        }

        if ($model->ip) {
            Cache::forget('ip-ban:'.md5($model->ip));
        }

        //event(new ContentTextUpdatedEvent($model));
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
