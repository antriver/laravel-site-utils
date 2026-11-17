<?php

namespace Antriver\LaravelSiteUtilsTestApp\Providers;

use Antriver\LaravelSiteUtils\Mail\MailStyles;
use Antriver\LaravelSiteUtils\Mail\MailStylesInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MailStylesInterface::class, MailStyles::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
