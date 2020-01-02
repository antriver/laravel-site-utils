<?php

namespace Antriver\SiteUtils\Laravel\Providers;

use Antriver\SiteUtils\Libraries\Debug\QueryLogger;
use Antriver\SiteUtils\Libraries\Laravel\Auth\RepositoryUserProvider;
use Antriver\SiteUtils\Repositories\Interfaces\UserRepositoryInterface;
use Auth;
use Config;
use DB;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Tmd\LaravelPasswordUpdater\PasswordHasher;
use Validator;

class SiteUtilsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        include_once dirname(dirname(__DIR__)).'/helpers.php';

        Auth::provider(
            'repository',
            function (Container $app) {
                return new RepositoryUserProvider(
                    $app->make(UserRepositoryInterface::class),
                    $app->make(PasswordHasher::class)
                );
            }
        );

        $this->registerQueryLogger();

        // This makes everything break if the DB is down. Disabled.
        //DB::connection()->getPdo()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        // Use a different guard based on the route.
        // You can use $request->user()
        // or inject Authenticable into classes
        // The only thing not working is injecting Authenticable into controller methods.
        // @see http://mattallan.org/2016/setting-the-guard-per-route-in-laravel/
        $this->app['router']->matched(
            function (\Illuminate\Routing\Events\RouteMatched $event) {
                $route = $event->route;
                if (!array_has($route->getAction(), 'guard')) {
                    return;
                }
                $routeGuard = array_get($route->getAction(), 'guard');
                $this->app['auth']->resolveUsersUsing(
                    function ($guard = null) use ($routeGuard) {
                        return $this->app['auth']->guard($routeGuard)->user();
                    }
                );
                $this->app['auth']->setDefaultDriver($routeGuard);
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    private function registerQueryLogger()
    {
        if (Config::get('app.log_queries')) {
            new QueryLogger();
        }
    }
}
