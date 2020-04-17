<?php

namespace Antriver\LaravelSiteScaffolding\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;

abstract class AbstractQueuedListener extends AbstractListener implements ShouldQueue
{

}
