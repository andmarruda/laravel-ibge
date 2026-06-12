<?php

namespace Andmarruda\LaravelIbge\Contracts;

interface CacheKeyFactory
{
    public function make(string $resource, array $parameters = []): string;
}
