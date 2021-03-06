<?php

namespace Antriver\LaravelSiteUtils\Events;

use Illuminate\Queue\SerializesModels;
use ReflectionClass;

abstract class AbstractEvent
{
    use SerializesModels;

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    public function broadcastAs()
    {
        $reflect = new ReflectionClass($this);

        return $reflect->getShortName();
    }
}
