<?php

namespace Antriver\SiteUtils\Debug\Events;

class LocalCacheHitEvent
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
