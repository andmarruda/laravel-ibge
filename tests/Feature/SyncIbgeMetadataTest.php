<?php

namespace Andmarruda\LaravelIbge\Tests\Feature;

use Andmarruda\LaravelIbge\Jobs\SyncIbgeMetadata;
use Andmarruda\LaravelIbge\Jobs\SyncOdsObjective;
use Andmarruda\LaravelIbge\Jobs\SyncPesquisaMetadata;
use Andmarruda\LaravelIbge\MetadataSynchronizer;
use Andmarruda\LaravelIbge\Tests\TestCase;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Bus;

final class SyncIbgeMetadataTest extends TestCase
{
    public function test_the_root_job_refreshes_the_list_and_dispatches_child_jobs(): void
    {
        Bus::fake();

        $this->app->make(Factory::class)->fake([
            '*/Pesquisa' => [['codigo' => 'CD'], ['codigo' => 'PNAD']],
        ]);

        (new SyncIbgeMetadata())->handle(
            $this->app->make(MetadataSynchronizer::class),
            $this->app->make(Dispatcher::class),
        );

        Bus::assertDispatched(SyncPesquisaMetadata::class, 2);
        Bus::assertDispatched(SyncOdsObjective::class, 17);
    }

    public function test_the_artisan_command_dispatches_the_root_job(): void
    {
        Bus::fake();

        $this->artisan('ibge:sync')->assertSuccessful();

        Bus::assertDispatched(SyncIbgeMetadata::class);
    }
}
