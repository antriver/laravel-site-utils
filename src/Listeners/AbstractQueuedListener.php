<?php

namespace Antriver\LaravelSiteUtils\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;

abstract class AbstractQueuedListener extends AbstractListener implements ShouldQueue
{

}
