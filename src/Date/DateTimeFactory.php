<?php

namespace Antriver\LaravelSiteScaffolding\Date;

use DateTimeImmutable;
use DateTimeZone;

/**
 * This should be used as the source of the current time by using DateTimeFactory::now() instead
 * of time(), new DateTime() etc.
 *
 * This helps with unit tests as the 'current' time can be faked by calling the setNow() method
 * with what you want the current time to be in tests. Any calls to now() will receive that time you specified
 * instead of the real current time.
 */
class DateTimeFactory
{
    /**
     * @var DateTimeImmutable|null
     */
    private static $now;

    /**
     * @var DateTimeZone|null
     */
    private static $timezone;

    /**
     * Get the current UTC time.
     *
     * @return DateTimeImmutable
     */
    public static function now(): DateTimeImmutable
    {
        if (self::$now !== null) {
            return clone self::$now;
        }

        return new DateTimeImmutable('now', self::timezone());
    }

    /**
     * Set the current time (probably in a test).
     * Can be set to null so the real current time is used again.
     *
     * @param DateTimeImmutable $now
     */
    public static function setNow(DateTimeImmutable $now)
    {
        self::$now = $now;
    }

    public static function clearNow()
    {
        self::$now = null;
    }

    public static function timezone(): DateTimeZone
    {
        if (self::$timezone === null) {
            self::$timezone = new DateTimeZone('Etc/Utc');
        }

        return self::$timezone;
    }
}
