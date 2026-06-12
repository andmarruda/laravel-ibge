<?php

namespace Andmarruda\LaravelIbge;

use Andmarruda\LaravelIbge\Console\SyncIbgeMetadataCommand;
use Andmarruda\LaravelIbge\Contracts\CacheKeyFactory;
use Andmarruda\LaravelIbge\Contracts\MetadataGateway;
use Andmarruda\LaravelIbge\Contracts\MetadataStore;
use Andmarruda\LaravelIbge\Gateways\CachedMetadataGateway;
use Andmarruda\LaravelIbge\Gateways\HttpMetadataGateway;
use Andmarruda\LaravelIbge\Stores\DatabaseMetadataStore;
use Andmarruda\LaravelIbge\Stores\NullMetadataStore;
use Andmarruda\LaravelIbge\Support\MetadataCache;
use Andmarruda\LaravelIbge\Support\VersionedCacheKeyFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;

final class IbgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ibge.php', 'ibge');

        $this->app->singleton(CacheKeyFactory::class, function (Application $app): CacheKeyFactory {
            return new VersionedCacheKeyFactory(
                (string) $app['config']->get('ibge.cache.prefix'),
                (string) $app['config']->get('ibge.cache.schema_version'),
            );
        });

        $this->app->singleton(HttpMetadataGateway::class, function (Application $app): HttpMetadataGateway {
            return new HttpMetadataGateway(
                $app->make(Factory::class),
                (string) $app['config']->get('ibge.base_url'),
                (int) $app['config']->get('ibge.http.timeout'),
                (int) $app['config']->get('ibge.http.connect_timeout'),
                (int) $app['config']->get('ibge.http.retry_times'),
                (int) $app['config']->get('ibge.http.retry_sleep_ms'),
            );
        });

        $this->app->singleton(MetadataCache::class, function (Application $app): MetadataCache {
            return new MetadataCache(
                $app['cache']->store($app['config']->get('ibge.cache.store')),
                $app->make(CacheKeyFactory::class),
                $app['config']->get('ibge.cache.ttl', []),
            );
        });

        $this->app->singleton(MetadataStore::class, function (Application $app): MetadataStore {
            if (! (bool) $app['config']->get('ibge.database.enabled')) {
                return new NullMetadataStore();
            }

            return new DatabaseMetadataStore(
                $app['db']->connection($app['config']->get('ibge.database.connection')),
            );
        });

        $this->app->singleton(MetadataGateway::class, function (Application $app): MetadataGateway {
            $gateway = $app->make(HttpMetadataGateway::class);

            if (! (bool) $app['config']->get('ibge.cache.enabled')) {
                return $gateway;
            }

            return new CachedMetadataGateway(
                $gateway,
                $app['cache']->store($app['config']->get('ibge.cache.store')),
                $app->make(CacheKeyFactory::class),
                $app['config']->get('ibge.cache.ttl', []),
            );
        });

        $this->app->singleton(Ibge::class, fn (Application $app): Ibge => new Ibge(
            $app->make(MetadataGateway::class),
        ));

        $this->app->alias(Ibge::class, 'ibge');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/ibge.php' => config_path('ibge.php'),
        ], 'ibge-config');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations/2026_01_01_000001_create_ibge_pesquisas_table.php'
                => database_path('migrations/2026_01_01_000001_create_ibge_pesquisas_table.php'),
            __DIR__.'/../database/migrations/2026_01_01_000002_create_ibge_pesquisa_ocorrencias_table.php'
                => database_path('migrations/2026_01_01_000002_create_ibge_pesquisa_ocorrencias_table.php'),
            __DIR__.'/../database/migrations/2026_01_01_000003_create_ibge_ods_metas_table.php'
                => database_path('migrations/2026_01_01_000003_create_ibge_ods_metas_table.php'),
            __DIR__.'/../database/migrations/2026_01_01_000004_create_ibge_ods_indicadores_table.php'
                => database_path('migrations/2026_01_01_000004_create_ibge_ods_indicadores_table.php'),
        ], 'ibge-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([SyncIbgeMetadataCommand::class]);
        }
    }
}
