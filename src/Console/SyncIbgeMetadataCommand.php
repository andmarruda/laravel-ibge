<?php

namespace Andmarruda\LaravelIbge\Console;

use Andmarruda\LaravelIbge\Jobs\SyncIbgeMetadata;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;

final class SyncIbgeMetadataCommand extends Command
{
    protected $signature = 'ibge:sync';

    protected $description = 'Enfileira a atualização completa do cache de metadados do IBGE';

    public function handle(Dispatcher $dispatcher): int
    {
        $job = new SyncIbgeMetadata();
        $job->onConnection(config('ibge.sync.connection'));
        $job->onQueue((string) config('ibge.sync.queue', 'default'));

        $dispatcher->dispatch($job);

        $this->components->info('Sincronização dos metadados do IBGE enfileirada.');

        return self::SUCCESS;
    }
}
