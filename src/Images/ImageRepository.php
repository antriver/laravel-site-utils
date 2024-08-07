<?php

namespace Antriver\LaravelSiteUtils\Images;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Antriver\LaravelRepositories\Base\AbstractCachedRepository;

class ImageRepository extends AbstractCachedRepository
{
    /**
     * Return the fully qualified class name of the Models this repository returns.
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return Image::class;
    }

    /**
     * @param string $originalUrl
     * @param bool $recent
     *
     * @return Image|null
     */
    public function getImageForOriginalUrl($originalUrl, $recent = true)
    {
        $query = $this->create()->newQuery()->where('originalUrl', $originalUrl);

        if ($recent) {
            $cutoff = (new Carbon('-1 MONTH'))->toDateTimeString();
            $query = $query->where('createdAt', '>=', $cutoff);
        }

        return $query->first();
    }

    public function persist(EloquentModel $model)
    {
        if ($model->exists && $model->isDirty(['filename', 'directory'])) {
            throw new \Exception("Filename and directory cannot be changed. Create a new Image.");
        }

        return parent::persist($model);
    }
}
