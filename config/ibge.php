<?php

return [
    'base_url' => env('IBGE_METADATA_BASE_URL', 'https://apimetadados.ibge.gov.br/api'),

    'http' => [
        'timeout' => (int) env('IBGE_METADATA_TIMEOUT', 15),
        'connect_timeout' => (int) env('IBGE_METADATA_CONNECT_TIMEOUT', 5),
        'retry_times' => (int) env('IBGE_METADATA_RETRY_TIMES', 2),
        'retry_sleep_ms' => (int) env('IBGE_METADATA_RETRY_SLEEP_MS', 200),
    ],

    'cache' => [
        'enabled' => env('IBGE_METADATA_CACHE_ENABLED', true),
        'store' => env('IBGE_METADATA_CACHE_STORE'),
        'prefix' => env('IBGE_METADATA_CACHE_PREFIX', 'ibge:metadata'),
        'schema_version' => 'v1',
        'ttl' => [
            'pesquisas' => (int) env('IBGE_METADATA_CACHE_TTL_PESQUISAS', 7776000),
            'ocorrencias' => (int) env('IBGE_METADATA_CACHE_TTL_OCORRENCIAS', 7776000),
            'ocorrencia' => (int) env('IBGE_METADATA_CACHE_TTL_OCORRENCIA', 7776000),
            'ods_metas' => (int) env('IBGE_METADATA_CACHE_TTL_ODS_METAS', 7776000),
            'ods_ficha_metodologica' => (int) env('IBGE_METADATA_CACHE_TTL_ODS_FICHA', 7776000),
        ],
    ],

    'sync' => [
        'connection' => env('IBGE_METADATA_SYNC_CONNECTION'),
        'queue' => env('IBGE_METADATA_SYNC_QUEUE', 'default'),
        'ods_objectives' => range(1, 17),
    ],

    'database' => [
        'enabled' => env('IBGE_METADATA_DATABASE_ENABLED', true),
        'connection' => env('IBGE_METADATA_DATABASE_CONNECTION'),
    ],
];
