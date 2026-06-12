<?php

namespace Andmarruda\LaravelIbge\Jobs;

use Andmarruda\LaravelIbge\Jobs\Concerns\DispatchesIbgeSyncJobs;
use Andmarruda\LaravelIbge\MetadataSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

final class SyncIbgeMetadata implements ShouldQueue
{
    use DispatchesIbgeSyncJobs;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function handle(MetadataSynchronizer $synchronizer, Dispatcher $dispatcher): void
    {
        $pesquisas = $synchronizer->pesquisas();

        collect($pesquisas)
            ->pluck('codigo')
            ->filter()
            ->unique()
            ->each(fn (string $codigo) => $this->dispatch($dispatcher, new SyncPesquisaMetadata($codigo)));

        foreach (config('ibge.sync.ods_objectives', range(1, 17)) as $objetivo) {
            $this->dispatch($dispatcher, new SyncOdsObjective((int) $objetivo));
        }
    }
}
