<?php

namespace Andmarruda\LaravelIbge\Jobs;

use Andmarruda\LaravelIbge\Jobs\Concerns\DispatchesIbgeSyncJobs;
use Andmarruda\LaravelIbge\MetadataSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

final class SyncOdsObjective implements ShouldQueue
{
    use DispatchesIbgeSyncJobs;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $numeroObjetivo)
    {
    }

    public function handle(MetadataSynchronizer $synchronizer, Dispatcher $dispatcher): void
    {
        collect($synchronizer->metas($this->numeroObjetivo))
            ->pluck('indicadores')
            ->flatten(1)
            ->pluck('numero')
            ->filter()
            ->unique()
            ->each(fn (string $numero) => $this->dispatch($dispatcher, new SyncOdsIndicator($numero)));
    }
}
