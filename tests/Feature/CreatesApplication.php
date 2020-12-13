<?php

namespace Antriver\LaravelSiteUtilsTests\Feature;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../test-laravel-app/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
