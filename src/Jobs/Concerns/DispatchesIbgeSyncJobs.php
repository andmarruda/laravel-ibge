<?php

namespace Andmarruda\LaravelIbge\Jobs\Concerns;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;

trait DispatchesIbgeSyncJobs
{
    private function dispatch(Dispatcher $dispatcher, ShouldQueue $job): void
    {
        $job->onConnection(config('ibge.sync.connection'));
        $job->onQueue((string) config('ibge.sync.queue', 'default'));

        $dispatcher->dispatch($job);
    }
}
