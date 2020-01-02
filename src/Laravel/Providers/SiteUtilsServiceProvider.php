<?php

namespace Antriver\SiteUtils\Laravel\Providers;

use Antriver\SiteUtils\Debug\QueryLogger;
use Antriver\SiteUtils\Laravel\Auth\RepositoryUserProvider;
use Antriver\SiteUtils\Repositories\Interfaces\UserRepositoryInterface;
use Auth;
use Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

class SiteUtilsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        include_once dirname(dirname(__DIR__)).'/helpers.php';

        // Register a 'repository' user provider.
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

        /** @var Router $router */
        $router = $this->app['router'];

        /** @var \Illuminate\Auth\AuthManager $auth */
        $auth = $this->app['auth'];

        $router->matched(
            function (RouteMatched $event) use ($auth) {
                $route = $event->route;
                if (!\Arr::has($route->getAction(), 'guard')) {
                    return;
                }
                $routeGuard = \Arr::get($route->getAction(), 'guard');
                $auth->resolveUsersUsing(
                    function ($guard = null) use ($auth, $routeGuard) {
                        return $auth->guard($routeGuard)->user();
                    }
                );
                $auth->setDefaultDriver($routeGuard);
            }
        );
    }

    private function registerQueryLogger()
    {
        if (Config::get('app.log_queries')) {
            new QueryLogger();
        }
    }
}
