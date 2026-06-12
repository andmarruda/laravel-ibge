<?php

namespace Andmarruda\LaravelIbge\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array pesquisas()
 * @method static array ocorrencias(string $codigoPesquisa)
 * @method static array ocorrencia(string $codigoPesquisa, int $ano, int $mes = 0, int $ordemPeriodo = 0)
 * @method static array metas(int $numeroObjetivo)
 * @method static array fichaMetodologica(string $numeroIndicador)
 */
final class Ibge extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Andmarruda\LaravelIbge\Ibge::class;
    }
}
