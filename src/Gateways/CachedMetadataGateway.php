<?php

namespace Andmarruda\LaravelIbge\Gateways;

use Andmarruda\LaravelIbge\Contracts\CacheKeyFactory;
use Andmarruda\LaravelIbge\Contracts\MetadataGateway;
use Illuminate\Contracts\Cache\Repository;

final class CachedMetadataGateway implements MetadataGateway
{
    public function __construct(
        private readonly MetadataGateway $gateway,
        private readonly Repository $cache,
        private readonly CacheKeyFactory $keys,
        private readonly array $ttl,
    ) {
    }

    public function pesquisas(): array
    {
        return $this->remember('pesquisas', [], fn (): array => $this->gateway->pesquisas());
    }

    public function ocorrencias(string $codigoPesquisa): array
    {
        return $this->remember(
            'ocorrencias',
            ['codigo_pesquisa' => $codigoPesquisa],
            fn (): array => $this->gateway->ocorrencias($codigoPesquisa),
        );
    }

    public function ocorrencia(string $codigoPesquisa, int $ano, int $mes = 0, int $ordemPeriodo = 0): array
    {
        return $this->remember(
            'ocorrencia',
            compact('codigoPesquisa', 'ano', 'mes', 'ordemPeriodo'),
            fn (): array => $this->gateway->ocorrencia($codigoPesquisa, $ano, $mes, $ordemPeriodo),
        );
    }

    public function metas(int $numeroObjetivo): array
    {
        return $this->remember(
            'ods_metas',
            ['numero_objetivo' => $numeroObjetivo],
            fn (): array => $this->gateway->metas($numeroObjetivo),
        );
    }

    public function fichaMetodologica(string $numeroIndicador): array
    {
        return $this->remember(
            'ods_ficha_metodologica',
            ['numero_indicador' => $numeroIndicador],
            fn (): array => $this->gateway->fichaMetodologica($numeroIndicador),
        );
    }

    private function remember(string $resource, array $parameters, callable $callback): array
    {
        return $this->cache->remember(
            $this->keys->make($resource, $parameters),
            (int) ($this->ttl[$resource] ?? 7776000),
            $callback,
        );
    }
}
