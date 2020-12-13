<?php

namespace Antriver\LaravelSiteUtils\Debug\Events;

class LocalKeyWrittenEvent
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
