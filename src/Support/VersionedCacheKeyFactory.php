<?php

namespace Andmarruda\LaravelIbge\Support;

use Andmarruda\LaravelIbge\Contracts\CacheKeyFactory;

final class VersionedCacheKeyFactory implements CacheKeyFactory
{
    public function __construct(
        private readonly string $prefix,
        private readonly string $schemaVersion,
    ) {
    }

    public function make(string $resource, array $parameters = []): string
    {
        ksort($parameters);

        $suffix = $parameters === []
            ? 'all'
            : hash('sha256', json_encode($parameters, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

        return implode(':', [
            trim($this->prefix, ':'),
            trim($this->schemaVersion, ':'),
            trim($resource, ':'),
            $suffix,
        ]);
    }
}
