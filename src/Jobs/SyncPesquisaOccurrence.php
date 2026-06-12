<?php

namespace Andmarruda\LaravelIbge\Jobs;

use Andmarruda\LaravelIbge\MetadataSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

final class SyncPesquisaOccurrence implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly string $codigoPesquisa,
        public readonly int $ano,
        public readonly int $mes = 0,
        public readonly int $ordemPeriodo = 0,
    ) {
    }

    public function handle(MetadataSynchronizer $synchronizer): void
    {
        $synchronizer->ocorrencia(
            $this->codigoPesquisa,
            $this->ano,
            $this->mes,
            $this->ordemPeriodo,
        );
    }
}
