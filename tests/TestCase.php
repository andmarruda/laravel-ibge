<?php

namespace Andmarruda\LaravelIbge\Tests;

use Andmarruda\LaravelIbge\IbgeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('ibge.database.enabled', false);
        $app['config']->set('cache.default', 'array');
    }

    protected function getPackageProviders($app): array
    {
        return [IbgeServiceProvider::class];
    }
}
