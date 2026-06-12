<?php

namespace Andmarruda\LaravelIbge\Jobs;

use Andmarruda\LaravelIbge\Jobs\Concerns\DispatchesIbgeSyncJobs;
use Andmarruda\LaravelIbge\MetadataSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

final class SyncPesquisaMetadata implements ShouldQueue
{
    use DispatchesIbgeSyncJobs;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly string $codigoPesquisa)
    {
    }

    public function handle(MetadataSynchronizer $synchronizer, Dispatcher $dispatcher): void
    {
        collect($synchronizer->ocorrencias($this->codigoPesquisa))
            ->filter(fn (array $ocorrencia): bool => isset($ocorrencia['ano']))
            ->each(function (array $ocorrencia) use ($dispatcher): void {
                $this->dispatch($dispatcher, new SyncPesquisaOccurrence(
                    $this->codigoPesquisa,
                    (int) $ocorrencia['ano'],
                    (int) ($ocorrencia['mes'] ?? 0),
                    (int) ($ocorrencia['ordem_periodo'] ?? 0),
                ));
            });
    }
}
