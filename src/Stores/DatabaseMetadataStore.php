<?php

namespace Andmarruda\LaravelIbge\Stores;

use Andmarruda\LaravelIbge\Contracts\MetadataStore;
use Illuminate\Database\ConnectionInterface;

final class DatabaseMetadataStore implements MetadataStore
{
    public function __construct(private readonly ConnectionInterface $database)
    {
    }

    public function pesquisas(array $pesquisas): void
    {
        $rows = array_values(array_filter(array_map(
            fn (array $pesquisa): ?array => isset($pesquisa['codigo']) ? [
                'codigo' => (string) $pesquisa['codigo'],
                'nome' => $pesquisa['nome'] ?? null,
                'nome_ingles' => $pesquisa['nome_ingles'] ?? null,
                'situacao' => $pesquisa['situacao'] ?? null,
                'categoria' => $pesquisa['categoria'] ?? null,
                'periodicidade_coleta' => $pesquisa['periodicidade_coleta'] ?? null,
                'periodicidade_divulgacao' => $pesquisa['periodicidade_divulgacao'] ?? null,
                ...$this->metadata($pesquisa),
            ] : null,
            $pesquisas,
        )));

        $this->upsert('ibge_pesquisas', $rows, ['codigo']);
    }

    public function ocorrencias(string $codigoPesquisa, array $ocorrencias): void
    {
        $rows = array_values(array_filter(array_map(
            fn (array $ocorrencia): ?array => isset($ocorrencia['ano']) ? [
                'codigo_pesquisa' => $codigoPesquisa,
                'ano' => (int) $ocorrencia['ano'],
                'mes' => (int) ($ocorrencia['mes'] ?? 0),
                'ordem_periodo' => (int) ($ocorrencia['ordem_periodo'] ?? 0),
                'nome_ocorrencia' => $ocorrencia['nome_ocorrencia'] ?? null,
                ...$this->metadata($ocorrencia),
            ] : null,
            $ocorrencias,
        )));

        $this->upsert(
            'ibge_pesquisa_ocorrencias',
            $rows,
            ['codigo_pesquisa', 'ano', 'mes', 'ordem_periodo'],
        );
    }

    public function ocorrencia(
        string $codigoPesquisa,
        int $ano,
        int $mes,
        int $ordemPeriodo,
        array $ocorrencia,
    ): void {
        $row = [
            'codigo_pesquisa' => $codigoPesquisa,
            'ano' => $ano,
            'mes' => $mes,
            'ordem_periodo' => $ordemPeriodo,
            ...$this->metadata($ocorrencia),
        ];

        if (isset($ocorrencia['nome_ocorrencia'])) {
            $row['nome_ocorrencia'] = $ocorrencia['nome_ocorrencia'];
        }

        $this->upsert(
            'ibge_pesquisa_ocorrencias',
            [$row],
            ['codigo_pesquisa', 'ano', 'mes', 'ordem_periodo'],
        );
    }

    public function metas(int $numeroObjetivo, array $metas): void
    {
        $rows = [];
        $indicadores = [];

        foreach ($metas as $meta) {
            if (! isset($meta['numero'])) {
                continue;
            }

            $rows[] = [
                'numero' => (string) $meta['numero'],
                'numero_objetivo' => $numeroObjetivo,
                'nome' => $meta['nome'] ?? null,
                'descricao' => $meta['descricao'] ?? null,
                ...$this->metadata($meta),
            ];

            foreach ($meta['indicadores'] ?? [] as $indicador) {
                if (! isset($indicador['numero'])) {
                    continue;
                }

                $indicadores[] = [
                    'numero' => (string) $indicador['numero'],
                    'numero_meta' => (string) $meta['numero'],
                    'nome' => $indicador['nome'] ?? null,
                    'descricao' => $indicador['descricao'] ?? null,
                    ...$this->metadata($indicador),
                ];
            }
        }

        $this->upsert('ibge_ods_metas', $rows, ['numero']);
        $this->upsert('ibge_ods_indicadores', $indicadores, ['numero']);
    }

    public function fichaMetodologica(string $numeroIndicador, array $ficha): void
    {
        $now = now();
        $query = $this->database->table('ibge_ods_indicadores')
            ->where('numero', str_replace('-', '.', $numeroIndicador));

        $values = [
            'ficha_metodologica' => json_encode($ficha, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'synced_at' => $now,
            'updated_at' => $now,
        ];

        if ($query->update($values) === 0) {
            $this->database->table('ibge_ods_indicadores')->insert([
                'numero' => str_replace('-', '.', $numeroIndicador),
                'payload' => json_encode([], JSON_THROW_ON_ERROR),
                'created_at' => $now,
                ...$values,
            ]);
        }
    }

    private function metadata(array $payload): array
    {
        $now = now();

        return [
            'payload' => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'synced_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function upsert(string $table, array $rows, array $uniqueBy): void
    {
        if ($rows === []) {
            return;
        }

        $this->database->table($table)->upsert(
            $rows,
            $uniqueBy,
            array_values(array_diff(array_keys($rows[0]), [...$uniqueBy, 'created_at'])),
        );
    }
}
