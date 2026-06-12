<?php

namespace Andmarruda\LaravelIbge\Contracts;

interface MetadataGateway
{
    public function pesquisas(): array;

    public function ocorrencias(string $codigoPesquisa): array;

    public function ocorrencia(string $codigoPesquisa, int $ano, int $mes = 0, int $ordemPeriodo = 0): array;

    public function metas(int $numeroObjetivo): array;

    public function fichaMetodologica(string $numeroIndicador): array;
}
