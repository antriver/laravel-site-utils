<?php

namespace Antriver\LaravelSiteUtils\Models\Base;

use Antriver\LaravelSiteUtils\Models\Traits\OutputsDatesTrait;
use DB;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Schema;

/**
 * @mixin \Eloquent
 */
class AbstractModel extends EloquentModel implements AbstractModelInterface
{
    use OutputsDatesTrait;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'createdAt';

    /**
     * The name of the "delete at" column (for soft deletes).
     *
     * @var string
     */
    const DELETED_AT = 'deletedAt';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updatedAt';

    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * Create a new pivot model instance. Returns our custom Pivot class instead of the default.
     *
     * @param \Illuminate\Database\Eloquent\Model $parent
     * @param array $attributes
     * @param string $table
     * @param bool $exists
     *
     * @return Pivot
     */
    public function newPivot(EloquentModel $parent, array $attributes, $table, $exists, $using = null)
    {
        return new Pivot($parent, $attributes, $table, $exists);
    }

    /**
     * Return the attributes in an array.
     * Here we convert DateTimes to ISO 8601 format.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        $array = $this->formatArrayDates($array);

        return $array;
    }

    /**
     * @param $column
     *
     * @return bool
     */
    public function hasColumn($column)
    {
        return Schema::hasColumn($this->getTable(), $column);
    }

    /**
     * @param $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        return array_key_exists($attribute, $this->attributes);
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return Str::camel(class_basename($this)).'Id';
    }

    /**
     * Increment or decrement a field atomically.
     *
     * @param $column
     * @param $amount
     * @param array $extra
     *
     * @return bool|int
     */
    public function adjust($column, $amount, array $extra = [])
    {
        if ($amount > 0) {
            return $this->increment($column, $amount, $extra);
        } elseif ($amount < 0) {
            return $this->decrement($column, abs($amount), $extra);
        }

        return false;
    }

    /**
     * Return a Model related to this Model by looking it up in the given repository.
     *
     * @param string $field Field on this model that is the PK of the related model.
     * @param string $repositoryClass
     *
     * @return mixed|null
     */
    protected function getRelationFromRepository($field, $repositoryClass)
    {
        $value = $this->getAttribute($field);

        if (empty($value)) {
            return null;
        }

        return app($repositoryClass)->find($this->{$field});
    }

    /**
     * @return int|null
     */
    public static function getMaxKey()
    {
        $model = new static();
        if (!$model->incrementing) {
            return null;
        }

        return DB::table($model->getTable())->max($model->getKeyName());
    }

    /**
     * @param $data
     *
     * @param bool $exists
     *
     * @return AbstractModel
     */
    public static function hydrateOne($data, bool $exists = true)
    {
        $model = new static();

        $model->setRawAttributes($data);
        $model->exists = $exists;

        return $model;
    }
}
