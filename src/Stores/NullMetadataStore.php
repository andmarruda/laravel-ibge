<?php

namespace Andmarruda\LaravelIbge\Stores;

use Andmarruda\LaravelIbge\Contracts\MetadataStore;

final class NullMetadataStore implements MetadataStore
{
    public function pesquisas(array $pesquisas): void
    {
    }

    public function ocorrencias(string $codigoPesquisa, array $ocorrencias): void
    {
    }

    public function ocorrencia(
        string $codigoPesquisa,
        int $ano,
        int $mes,
        int $ordemPeriodo,
        array $ocorrencia,
    ): void {
    }

    public function metas(int $numeroObjetivo, array $metas): void
    {
    }

    public function fichaMetodologica(string $numeroIndicador, array $ficha): void
    {
    }
}
