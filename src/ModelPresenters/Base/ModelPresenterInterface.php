<?php

namespace Antriver\LaravelSiteUtils\ModelPresenters\Base;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ModelPresenterInterface
{
    /**
     * @param AbstractModel $model
     *
     * @return array|null
     */
    public function present(AbstractModel $model): ?array;

    /**
     * @param Model[]|\Iterator|Collection $models
     *
     * @return array[]
     */
    public function presentArray($models): array;
}
