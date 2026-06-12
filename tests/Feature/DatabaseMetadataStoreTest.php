<?php

namespace Andmarruda\LaravelIbge\Tests\Feature;

use Andmarruda\LaravelIbge\Models\OdsIndicador;
use Andmarruda\LaravelIbge\Models\OdsMeta;
use Andmarruda\LaravelIbge\Models\Pesquisa;
use Andmarruda\LaravelIbge\Models\PesquisaOcorrencia;
use Andmarruda\LaravelIbge\Stores\DatabaseMetadataStore;
use Andmarruda\LaravelIbge\Tests\TestCase;

final class DatabaseMetadataStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate')->run();
    }

    public function test_it_upserts_all_metadata_resources(): void
    {
        $store = new DatabaseMetadataStore($this->app['db']->connection());

        $store->pesquisas([['codigo' => 'CD', 'nome' => 'Censo Demográfico']]);
        $store->ocorrencias('CD', [[
            'ano' => 2010,
            'mes' => 0,
            'ordem_periodo' => 0,
            'nome_ocorrencia' => 'Censo 2010',
        ]]);
        $store->ocorrencia('CD', 2010, 0, 0, ['situacao' => 'Ativo']);
        $store->metas(6, [[
            'numero' => '6.1',
            'nome' => 'Água segura',
            'indicadores' => [['numero' => '6.1.1', 'nome' => 'Abastecimento seguro']],
        ]]);
        $store->fichaMetodologica('6-1-1', [['objetivo' => 'Água potável']]);

        self::assertSame('Censo Demográfico', Pesquisa::findOrFail('CD')->nome);
        self::assertSame('Censo 2010', PesquisaOcorrencia::firstOrFail()->nome_ocorrencia);
        self::assertSame('Ativo', PesquisaOcorrencia::firstOrFail()->payload['situacao']);
        self::assertSame('Água segura', OdsMeta::findOrFail('6.1')->nome);
        self::assertSame('Água potável', OdsIndicador::findOrFail('6.1.1')->ficha_metodologica[0]['objetivo']);
    }
}
