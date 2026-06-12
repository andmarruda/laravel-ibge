<?php

namespace Andmarruda\LaravelIbge;

use Andmarruda\LaravelIbge\Contracts\MetadataGateway;

final class Ibge
{
    public function __construct(private readonly MetadataGateway $metadata)
    {
    }

    public function pesquisas(): array
    {
        return $this->metadata->pesquisas();
    }

    public function ocorrencias(string $codigoPesquisa): array
    {
        return $this->metadata->ocorrencias($codigoPesquisa);
    }

    public function ocorrencia(string $codigoPesquisa, int $ano, int $mes = 0, int $ordemPeriodo = 0): array
    {
        return $this->metadata->ocorrencia($codigoPesquisa, $ano, $mes, $ordemPeriodo);
    }

    public function metas(int $numeroObjetivo): array
    {
        return $this->metadata->metas($numeroObjetivo);
    }

    public function fichaMetodologica(string $numeroIndicador): array
    {
        return $this->metadata->fichaMetodologica($numeroIndicador);
    }
}
