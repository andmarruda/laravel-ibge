<?php

namespace Andmarruda\LaravelIbge\Jobs;

use Andmarruda\LaravelIbge\MetadataSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

final class SyncOdsIndicator implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly string $numeroIndicador)
    {
    }

    public function handle(MetadataSynchronizer $synchronizer): void
    {
        $synchronizer->fichaMetodologica(str_replace('.', '-', $this->numeroIndicador));
    }
}
