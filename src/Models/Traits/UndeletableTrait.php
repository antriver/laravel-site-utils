<?php

namespace Antriver\LaravelSiteScaffolding\Models\Traits;

use ReflectionClass;

trait UndeletableTrait
{
    /**
     * @throws \Exception
     */
    public function delete()
    {
        $reflect = new ReflectionClass($this);
        $className = $reflect->getShortName();

        throw new \Exception("Cannot delete a {$className}");
    }
}
