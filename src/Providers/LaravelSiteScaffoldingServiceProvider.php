<?php

namespace Antriver\LaravelSiteScaffolding\Providers;

use Antriver\LaravelSiteScaffolding\Auth\RepositoryUserProvider;
use Antriver\LaravelSiteScaffolding\Bans\BanPresenter;
use Antriver\LaravelSiteScaffolding\Bans\BanRepository;
use Antriver\LaravelSiteScaffolding\Bans\BanRepositoryInterface;
use Antriver\LaravelSiteScaffolding\Debug\QueryLogger;
use Antriver\LaravelSiteScaffolding\Users\UserPresenter;
use Antriver\LaravelSiteScaffolding\Users\UserPresenterInterface;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Antriver\LaravelSiteScaffolding\Users\UserRepositoryInterface;
use Auth;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

class LaravelSiteScaffoldingServiceProvider extends ServiceProvider
{
    protected $concreteBindings = [
        BanPresenterInterface::class => BanPresenter::class,
        BanRepositoryInterface::class => BanRepository::class,
        UserPresenterInterface::class => UserPresenter::class,
        UserRepositoryInterface::class => UserRepository::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        include_once dirname(__DIR__).'/helpers.php';

        // This makes everything break if the DB is down. Disabled.
        //DB::connection()->getPdo()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        $this->bindInterfaces();
        $this->setupRepositoryUserProvider();
        $this->setupQueryLogger();
        $this->setupGuardsPerRoute();
    }

    protected function setupRepositoryUserProvider()
    {
        // Register a 'repository' user provider.
        // A service provider prior to this one should have registered UserRepositoryInterface with the DI container.
        Auth::provider(
            'repository',
            function (Container $app) {
                return new RepositoryUserProvider(
                    $app->make(UserRepositoryInterface::class),
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

    protected function bindInterfaces()
    {
        $this->app->singleton(BanRepositoryInterface::class, $this->getConcreteBanRepositoryClassName());
        $this->app->singleton(UserPresenterInterface::class, $this->getConcreteUserRepositoryClassName());
        $this->app->singleton(UserRepositoryInterface::class, $this->getConcreteUserRepositoryClassName());
    }

    protected function getConcreteBanRepositoryClassName(): string
    {
        return BanRepository::class;
    }

    protected function getConcreteUserRepositoryClassName(): string
    {
        return UserRepository::class;
    }

    protected function setupQueryLogger()
    {
        if (config('app.log_queries')) {
            $this->app->singleton(QueryLogger::class, new QueryLogger());
        }
    }
}
