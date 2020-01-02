<?php

namespace Antriver\LaravelSiteUtils\Pagination;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait ReturnsPaginatorsTrait
{
    /**
     * @param QueryBuilder|EloquentBuilder $builder
     * @param int $currentPage
     * @param int $perPage
     * @param bool $canJumpToLast
     *
     * @param int|null $totalItems
     *
     * @return LengthAwarePaginator
     */
    protected function getLengthAwarePaginatorFromBuilder(
        $builder,
        $currentPage = 1,
        $perPage = 15,
        $canJumpToLast = true,
        $totalItems = null
    ) {
        $query = $builder->toBase();

        if ($totalItems === null) {
            $totalItems = $query->getCountForPagination();
        }

        $items = $builder->forPage($currentPage, $perPage)->get();

        // Note: path is not set on the paginator so call setPath() on the returned paginator before use.

        if ($canJumpToLast) {
            return new LengthAwarePaginator($items, $totalItems, $perPage, $currentPage);
        } else {
            return new LastHiddenLengthAwarePaginator($items, $totalItems, $perPage, $currentPage);
        }
    }

    protected function getEmptyLengthAwarePaginator(
        $currentPage = 1,
        $perPage = 15,
        $canJumpToLast = true
    ) {
        if ($canJumpToLast) {
            return new LengthAwarePaginator([], 0, $perPage, $currentPage);
        } else {
            return new LastHiddenLengthAwarePaginator([], 0, $perPage, $currentPage);
        }
    }
}
