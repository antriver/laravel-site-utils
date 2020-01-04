<?php

namespace Antriver\LaravelSiteScaffolding\Debug\Events;

class LocalKeyWrittenEvent
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
