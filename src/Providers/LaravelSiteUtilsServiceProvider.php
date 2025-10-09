<?php

namespace Antriver\LaravelSiteUtils\Providers;

use Antriver\LaravelSiteUtils\Auth\RepositoryUserProvider;
use Antriver\LaravelSiteUtils\Console\Commands\SiteUtils\CleanDefaultFiles;
use Antriver\LaravelSiteUtils\Console\Commands\SiteUtils\InstallCommand;
use Antriver\LaravelSiteUtils\Console\Commands\SiteUtils\PublishTestsCommand;
use Antriver\LaravelSiteUtils\Debug\QueryLogger;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerification;
use Antriver\LaravelSiteUtils\EmailVerification\EmailVerificationPolicy;
use Antriver\LaravelSiteUtils\Users\PasswordHasher;
use Antriver\LaravelSiteUtils\Users\User;
use Antriver\LaravelSiteUtils\Users\UserPolicy;
use Antriver\LaravelSiteUtils\Users\UserRepository;
use Auth;
use Gate;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class LaravelSiteUtilsServiceProvider extends ServiceProvider
{
    protected $policies = [
        EmailVerification::class => EmailVerificationPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        include_once dirname(__DIR__).'/helpers.php';

        if (!defined('LARAVEL_START')) {
            // LARAVEL_START is not defined when running tests.
            define('LARAVEL_START', microtime(true));
        }

        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    CleanDefaultFiles::class,
                    InstallCommand::class,
                    PublishTestsCommand::class,
                ]
            );
        }

        // This makes everything break if the DB is down. Disabled.
        //DB::connection()->getPdo()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        $this->bindInterfaces();
        $this->setupRepositoryUserProvider();
        $this->setupQueryLogger();
        $this->setupGuardsPerRoute();
        $this->registerPolicies();
    }

    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');
    }

    protected function setupRepositoryUserProvider()
    {
        // Register a 'repository' user provider.
        Auth::provider(
            'repository',
            function (Container $app) {
                return new RepositoryUserProvider(
                    $app->make(UserRepository::class),
                    $app->make(PasswordHasher::class)
                );
            }
        );
    }

    /**
     * Use a different guard based on the route.
     * Each route should be in a group that specifies which guard to use.
     *
     * This makes $request->user() or injecting Authenticable work.
     * The only thing not working is injecting Authenticable into controller methods.
     *
     * @see http://mattallan.org/2016/setting-the-guard-per-route-in-laravel/
     */
    protected function setupGuardsPerRoute()
    {
        /** @var Router $router */
        $router = $this->app['router'];

        /** @var \Illuminate\Auth\AuthManager $auth */
        $auth = $this->app['auth'];

        $router->matched(
            function (RouteMatched $event) use ($auth) {
                $route = $event->route;
                if (!Arr::has($route->getAction(), 'guard')) {
                    return;
                }
                $routeGuard = Arr::get($route->getAction(), 'guard');
                $auth->resolveUsersUsing(
                    function ($guard = null) use ($auth, $routeGuard) {
                        return $auth->guard($routeGuard)->user();
                    }
                );
                $auth->setDefaultDriver($routeGuard);
            }
        );
    }

    /**
     * Bind lots of things in Laravel's DI container as singletons.
     */
    protected function bindInterfaces()
    {
        /*foreach ($this->concreteBindings as $interface => $concrete) {
            $this->app->singleton($concrete);
            $this->app->singleton($interface, $concrete);
        }*/
    }

    protected function setupQueryLogger()
    {
        if (config('app.log_queries')) {
            $this->app->singleton(QueryLogger::class);
        }
    }

    protected function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }
}
