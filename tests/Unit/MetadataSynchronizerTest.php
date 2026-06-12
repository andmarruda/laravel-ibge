<?php

namespace Andmarruda\LaravelIbge\Tests\Unit;

use Andmarruda\LaravelIbge\Gateways\HttpMetadataGateway;
use Andmarruda\LaravelIbge\MetadataSynchronizer;
use Andmarruda\LaravelIbge\Stores\NullMetadataStore;
use Andmarruda\LaravelIbge\Support\MetadataCache;
use Andmarruda\LaravelIbge\Support\VersionedCacheKeyFactory;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Http\Client\Factory;
use PHPUnit\Framework\TestCase;

final class MetadataSynchronizerTest extends TestCase
{
    public function test_it_forces_cache_refresh_even_when_the_key_exists(): void
    {
        $http = new Factory();
        $http->fakeSequence()
            ->push([['codigo' => 'OLD']])
            ->push([['codigo' => 'NEW']]);

        $repository = new Repository(new ArrayStore());
        $keys = new VersionedCacheKeyFactory('ibge', 'v1');
        $synchronizer = new MetadataSynchronizer(
            new HttpMetadataGateway($http, 'https://example.test/api', 15, 5, 1, 0),
            new MetadataCache($repository, $keys, ['pesquisas' => 7776000]),
            new NullMetadataStore(),
        );

        $synchronizer->pesquisas();
        $synchronizer->pesquisas();

        self::assertSame(
            [['codigo' => 'NEW']],
            $repository->get($keys->make('pesquisas')),
        );
    }
}
