<?php

namespace Antriver\LaravelSiteUtils\Debug\Events;

class LocalCacheMissedEvent
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
