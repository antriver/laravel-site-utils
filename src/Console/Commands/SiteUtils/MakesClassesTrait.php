<?php

namespace Antriver\LaravelSiteUtils\Console\Commands\SiteUtils;

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
        $dir = explode('/', $path);
        array_pop($dir);
        $dir = implode('/', $dir);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            $this->info("Created directory {$dir}");
        }

        if (!file_exists($path)) {
            file_put_contents($path, $contents);
            $this->info("Wrote {$path}");
        } else {
            $this->output->writeln("{$path} already exists");
        }
    }
}
