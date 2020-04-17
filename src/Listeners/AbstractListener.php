<?php

namespace Antriver\LaravelSiteScaffolding\Listeners;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

abstract class AbstractListener
{
    protected function log($context = [])
    {
        if (\Config::get('app.log_jobs')) {
            $this->getLogger()->debug(static::class, $context);
        }
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        $jobLogger = new Logger('Listeners');

        $lineFormatter = new LineFormatter(
            "[%datetime%] %message% %context% %extra%\n",
            null,
            true,
            true
        );

        $fileHandler = new RotatingFileHandler(storage_path().'/logs/listeners.log');
        $fileHandler->setFormatter($lineFormatter);
        $jobLogger->pushHandler($fileHandler);

        return $jobLogger;
    }
}
