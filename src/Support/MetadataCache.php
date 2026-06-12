<?php

namespace Andmarruda\LaravelIbge\Support;

use Andmarruda\LaravelIbge\Contracts\CacheKeyFactory;
use Illuminate\Contracts\Cache\Repository;

final class MetadataCache
{
    public function __construct(
        private readonly Repository $cache,
        private readonly CacheKeyFactory $keys,
        private readonly array $ttl,
    ) {
    }

    public function put(string $resource, array $parameters, array $value): array
    {
        $this->cache->put(
            $this->keys->make($resource, $parameters),
            $value,
            (int) ($this->ttl[$resource] ?? 7776000),
        );

        return $value;
    }
}
