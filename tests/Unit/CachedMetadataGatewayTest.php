<?php

namespace Andmarruda\LaravelIbge\Tests\Unit;

use Andmarruda\LaravelIbge\Gateways\CachedMetadataGateway;
use Andmarruda\LaravelIbge\Support\VersionedCacheKeyFactory;
use Andmarruda\LaravelIbge\Tests\Support\StubMetadataGateway;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use PHPUnit\Framework\TestCase;

final class CachedMetadataGatewayTest extends TestCase
{
    public function test_it_caches_each_resource_by_its_parameters(): void
    {
        $source = new StubMetadataGateway();
        $gateway = new CachedMetadataGateway(
            $source,
            new Repository(new ArrayStore()),
            new VersionedCacheKeyFactory('ibge', 'v1'),
            ['ocorrencias' => 60],
        );

        $gateway->ocorrencias('CD');
        $gateway->ocorrencias('CD');
        $gateway->ocorrencias('PNAD');

        self::assertSame(2, $source->calls);
    }
}
