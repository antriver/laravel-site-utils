<?php

namespace Antriver\LaravelSiteScaffolding\Debug;

use Antriver\LaravelSiteScaffolding\Debug\Events\LocalCacheHitEvent;
use Antriver\LaravelSiteScaffolding\Debug\Events\LocalCacheMissedEvent;
use Antriver\LaravelSiteScaffolding\Debug\Events\LocalKeyWrittenEvent;
use Carbon\Carbon;
use Event;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\Events\QueryExecuted;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class QueryLogger
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('Queries');

        $rotatingFileHandler = new RotatingFileHandler(
            storage_path().'/logs/queries.log',
            5,
            \Monolog\Logger::DEBUG,
            true,
            0777,
            true
        );

        // We write to a BufferHandler first as multiple requests at the same time will cause the logs
        // to be mixed together.
        $bufferHandler = new BufferHandler(
            $rotatingFileHandler
        );

        $lineFormatter = new LineFormatter("%message% %context% %extra%\n", null, true, true);
        $rotatingFileHandler->setFormatter($lineFormatter);

        $this->logger->pushHandler($bufferHandler);
        //$queryLogger->pushHandler(new StreamHandler("php://output"));

        if (php_sapi_name() !== 'cli') {
            $this->logger->info(
                "\n\n=======\n{$_SERVER['REQUEST_METHOD']}\n{$_SERVER['REQUEST_URI']}"
                //." \n".Request::server('HTTP_REFERER')
                ."\n".(new Carbon())->toDateTimeString()
                ."\n========="
            );
        }

        Event::listen(
            CommandStarting::class,
            function (CommandStarting $event) {
                $this->logger->info(
                    "\n\n=======\n{$event->command}"
                    ."\n".(new Carbon())->toDateTimeString()
                    ."\n========="
                );
            }
        );

        Event::listen(
            CacheMissed::class,
            function (CacheMissed $event) {
                if ($event->key === 'illuminate:queue:restart') {
                    return false;
                }

                return $this->logger->info("cache.missed\t\t\t{$event->key}");
            }
        );

        Event::listen(
            CacheHit::class,
            function (CacheHit $event) {
                if ($event->key === 'illuminate:queue:restart') {
                    return;
                }

                $this->logger->info("cache.hit\t\t\t{$event->key}");
            }
        );

        Event::listen(
            KeyWritten::class,
            function (KeyWritten $event) {
                $this->logger->info("cache.write\t\t\t{$event->key}");
            }
        );

        Event::listen(
            KeyForgotten::class,
            function (KeyForgotten $event) {
                $this->logger->info("cache.forget\t\t\t{$event->key}");
            }
        );

        Event::listen(
            QueryExecuted::class,
            function (QueryExecuted $event) {

                $query = $event->sql;
                $bindings = $event->bindings;

                // Format binding data for sql insertion
                foreach ($bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $bindings[$i] = "'$binding'";
                        }
                    }
                }

                // Insert bindings into query
                $query = str_replace(['%', '?'], ['%%', '%s'], $query);
                $query = vsprintf($query, $bindings);

                $this->logger->info("query\t\t{$query}", [$event->time]);
            }
        );


        Event::listen(
            LocalCacheMissedEvent::class,
            function (LocalCacheMissedEvent $event) {
                $this->logger->info("array-cache.missed\t{$event->key}");
            }
        );

        Event::listen(
            LocalCacheHitEvent::class,
            function (LocalCacheHitEvent $event) {
                $this->logger->info("array-cache.hit\t\t{$event->key}");
            }
        );

        Event::listen(
            LocalKeyWrittenEvent::class,
            function (LocalKeyWrittenEvent $event) {
                $this->logger->info("array-cache.write\t{$event->key}");
            }
        );

    }
}
