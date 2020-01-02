<?php

namespace Antriver\LaravelSiteUtils\Users;

use Antriver\LaravelSiteUtils\Entities\User\UserInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tmd\LaravelRepositories\Interfaces\RepositoryInterface;

/**
 * Marker interface to help with injecting the correct repository that returns
 * User models.
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Return a model by its primary key.
     *
     * @param int $modelId
     *
     * @return UserInterface|null
     */
    public function find(int $modelId);

    /**
     * Return a model by its primary key or throw an exception if not found.
     *
     * @param int $modelId
     *
     * @return UserInterface
     */
    public function findOrFail(int $modelId);

    /**
     * Return a multiple models by their primary keys.
     *
     * @param int[] $modelIds
     *
     * @return UserInterface[]|Collection
     */
    public function findMany(array $modelIds);

    /**
     * Return a model by matching the specified field.
     *
     * @param string $field
     * @param mixed $value
     *
     * @return UserInterface|null
     */
    public function findOneBy(string $field, $value);

    /**
     * Return a model by matching the specified field or throw an exception if not found.
     *
     * @param string $field
     * @param mixed $value
     *
     * @return UserInterface
     */
    public function findOneByOrFail(string $field, $value);

    /**
     * Save a model to the database.
     *
     * @param Model|UserInterface $model
     *
     * @return bool
     */
    public function persist(Model $model);

    /**
     * Delete a model from the database.
     *
     * @param Model|UserInterface $model
     *
     * @return bool
     */
    public function remove(Model $model);
}
