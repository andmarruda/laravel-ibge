<?php

namespace Andmarruda\LaravelIbge\Tests\Unit;

use Andmarruda\LaravelIbge\Support\VersionedCacheKeyFactory;
use PHPUnit\Framework\TestCase;

final class VersionedCacheKeyFactoryTest extends TestCase
{
    public function test_it_builds_versioned_deterministic_keys(): void
    {
        $factory = new VersionedCacheKeyFactory('ibge:metadata', 'v1');

        $first = $factory->make('ocorrencia', ['ano' => 2010, 'codigo' => 'CD']);
        $second = $factory->make('ocorrencia', ['codigo' => 'CD', 'ano' => 2010]);

        self::assertSame($first, $second);
        self::assertStringStartsWith('ibge:metadata:v1:ocorrencia:', $first);
        self::assertSame('ibge:metadata:v1:pesquisas:all', $factory->make('pesquisas'));
    }
}
