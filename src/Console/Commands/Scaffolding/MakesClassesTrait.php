<?php

namespace Antriver\LaravelSiteScaffolding\Console\Commands\Scaffolding;

trait MakesClassesTrait
{
    protected function getAppNamespace(): string
    {
        return trim(app()->getNamespace(), '\\');
    }

    protected function getAppPath(): string
    {
        return app()->basePath();
    }

    protected function writeFileIfNotExists($path, $contents)
    {
        if (!file_exists($path)) {
            file_put_contents($path, $contents);
            $this->info("Wrote {$path}");
        } else {
            $this->output->writeln("{$path} already exists");
        }
    }
}
