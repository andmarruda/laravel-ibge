<?php

namespace Andmarruda\LaravelIbge;

use Andmarruda\LaravelIbge\Contracts\MetadataStore;
use Andmarruda\LaravelIbge\Gateways\HttpMetadataGateway;
use Andmarruda\LaravelIbge\Support\MetadataCache;

final class MetadataSynchronizer
{
    public function __construct(
        private readonly HttpMetadataGateway $gateway,
        private readonly MetadataCache $cache,
        private readonly MetadataStore $store,
    ) {
    }

    public function pesquisas(): array
    {
        $pesquisas = $this->gateway->pesquisas();
        $this->store->pesquisas($pesquisas);

        return $this->cache->put('pesquisas', [], $pesquisas);
    }

    public function ocorrencias(string $codigoPesquisa): array
    {
        $ocorrencias = $this->gateway->ocorrencias($codigoPesquisa);
        $this->store->ocorrencias($codigoPesquisa, $ocorrencias);

        return $this->cache->put(
            'ocorrencias',
            ['codigo_pesquisa' => $codigoPesquisa],
            $ocorrencias,
        );
    }

    public function ocorrencia(string $codigoPesquisa, int $ano, int $mes = 0, int $ordemPeriodo = 0): array
    {
        $ocorrencia = $this->gateway->ocorrencia($codigoPesquisa, $ano, $mes, $ordemPeriodo);
        $this->store->ocorrencia($codigoPesquisa, $ano, $mes, $ordemPeriodo, $ocorrencia);

        return $this->cache->put(
            'ocorrencia',
            compact('codigoPesquisa', 'ano', 'mes', 'ordemPeriodo'),
            $ocorrencia,
        );
    }

    public function metas(int $numeroObjetivo): array
    {
        $metas = $this->gateway->metas($numeroObjetivo);
        $this->store->metas($numeroObjetivo, $metas);

        return $this->cache->put(
            'ods_metas',
            ['numero_objetivo' => $numeroObjetivo],
            $metas,
        );
    }

    public function fichaMetodologica(string $numeroIndicador): array
    {
        $ficha = $this->gateway->fichaMetodologica($numeroIndicador);
        $this->store->fichaMetodologica($numeroIndicador, $ficha);

        return $this->cache->put(
            'ods_ficha_metodologica',
            ['numero_indicador' => $numeroIndicador],
            $ficha,
        );
    }
}
