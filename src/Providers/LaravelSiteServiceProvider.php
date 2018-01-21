<?php

namespace Tmd\LaravelSite\Providers;

use Auth;
use Config;
use DB;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use PDO;
use Tmd\LaravelPasswordUpdater\PasswordHasher;
use Tmd\LaravelSite\Libraries\Debug\QueryLogger;
use Tmd\LaravelSite\Libraries\Laravel\Auth\DatabaseSessionGuard;
use Tmd\LaravelSite\Libraries\Laravel\Auth\RepositoryUserProvider;
use Tmd\LaravelSite\Repositories\Interfaces\UserRepositoryInterface;
use Validator;

class LaravelSiteServiceProvider extends ServiceProvider
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

        include_once dirname(__DIR__).'/Libraries/helpers.php';

        Auth::provider(
            'repository',
            function (Container $app) {
                return new RepositoryUserProvider(
                    $app->make(UserRepositoryInterface::class),
                    $app->make(PasswordHasher::class)
                );
            }
        );

        Auth::extend(
            'database-session',
            function (Container $app, $name, array $config) {
                return new DatabaseSessionGuard(
                    app('auth')->createUserProvider($config['provider']),
                    $app->make(Request::class)
                );
            }
        );

        $this->registerQueryLogger();
        DB::connection()->getPdo()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

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
