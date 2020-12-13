<?php

use Antriver\LaravelSiteUtils\Date\DateFormat;

if (!function_exists('display_datetime')) {
    /**
     * @param DateTime $dateTime
     *
     * @return null|string Nov 10th 2017 10:51 AM
     */
    function display_datetime(DateTime $dateTime = null)
    {
        if (!$dateTime) {
            return null;
        }

        return $dateTime->format(DateFormat::DATE_TIME);
    }
}

if (!function_exists('display_date')) {
    /**
     * @param DateTime $dateTime
     *
     * @return string|null Nov 10th 2017
     */
    function display_date(DateTime $dateTime = null)
    {
        if (!$dateTime) {
            return null;
        }

        return $dateTime->format(DateFormat::DATE_ONLY);
    }
}

if (!function_exists('view_path')) {
    /**
     * @return string
     */
    function view_path()
    {
        return resource_path('views');
    }
}

if (!function_exists('asset_url')) {
    /**
     * @param string $path
     * @param bool $external
     *
     * @return string
     */
    function asset_url($path, $external = false)
    {
        return ($external ? config('app.assets_url') : config('app.assets_url')).'/'.$path;
    }
}

if (!function_exists('data_url')) {
    /**
     * Generate an absolute url to an item stored in remote storage.
     *
     * @param string $path
     *
     * @return string
     */
    function data_url($path)
    {
        $path = ltrim($path, '/');

        return rtrim(config('app.data_url').'/'.$path, '/');
    }
}

if (!function_exists('www_url')) {
    /**
     * @param string $path
     *
     * @return string
     */
    function www_url($path)
    {
        $path = ltrim($path, '/');

        return rtrim(config('app.url').'/'.$path, '/');
    }
}
