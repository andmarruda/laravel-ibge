<?php

namespace Andmarruda\LaravelIbge\Tests\Support;

use Andmarruda\LaravelIbge\Contracts\MetadataGateway;

final class StubMetadataGateway implements MetadataGateway
{
    public int $calls = 0;

    public function pesquisas(): array
    {
        return $this->respond([['codigo' => 'CD']]);
    }

    public function ocorrencias(string $codigoPesquisa): array
    {
        return $this->respond([['ano' => 2010]]);
    }

    public function ocorrencia(string $codigoPesquisa, int $ano, int $mes = 0, int $ordemPeriodo = 0): array
    {
        return $this->respond(['sigla' => $codigoPesquisa, 'ano' => $ano]);
    }

    public function metas(int $numeroObjetivo): array
    {
        return $this->respond([['numero' => $numeroObjetivo.'.1']]);
    }

    public function fichaMetodologica(string $numeroIndicador): array
    {
        return $this->respond([['indicador' => $numeroIndicador]]);
    }

    private function respond(array $response): array
    {
        $this->calls++;

        return $response;
    }
}
