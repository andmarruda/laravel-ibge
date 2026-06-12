<?php

namespace Andmarruda\LaravelIbge\Tests\Feature;

use Andmarruda\LaravelIbge\Contracts\MetadataGateway;
use Andmarruda\LaravelIbge\Gateways\CachedMetadataGateway;
use Andmarruda\LaravelIbge\Ibge;
use Andmarruda\LaravelIbge\IbgeServiceProvider;
use Andmarruda\LaravelIbge\Tests\TestCase;
use Illuminate\Support\ServiceProvider;

final class IbgeServiceProviderTest extends TestCase
{
    public function test_it_registers_the_public_client_and_cached_gateway(): void
    {
        self::assertInstanceOf(Ibge::class, $this->app->make(Ibge::class));
        self::assertInstanceOf(CachedMetadataGateway::class, $this->app->make(MetadataGateway::class));
        self::assertSame($this->app->make(Ibge::class), $this->app->make('ibge'));
    }

    public function test_it_exposes_all_migrations_for_publishing(): void
    {
        $migrations = ServiceProvider::pathsToPublish(IbgeServiceProvider::class, 'ibge-migrations');

        self::assertCount(4, $migrations);

        foreach (array_keys($migrations) as $source) {
            self::assertFileExists($source);
        }
    }
}
