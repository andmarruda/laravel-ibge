<?php

namespace Andmarruda\LaravelIbge\Tests\Unit;

use Andmarruda\LaravelIbge\Gateways\HttpMetadataGateway;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\TestCase;

final class HttpMetadataGatewayTest extends TestCase
{
    public function test_it_maps_the_public_methods_to_ibge_endpoints(): void
    {
        $http = new Factory();
        $http->fake([
            '*' => $http->response(['ok' => true]),
        ]);

        $gateway = new HttpMetadataGateway(
            $http,
            'https://apimetadados.ibge.gov.br/api',
            15,
            5,
            1,
            0,
        );

        $gateway->pesquisas();
        $gateway->ocorrencias('CD');
        $gateway->ocorrencia('CD', 2010);
        $gateway->metas(6);
        $gateway->fichaMetodologica('6-1-1');

        $expected = [
            'https://apimetadados.ibge.gov.br/api/Pesquisa',
            'https://apimetadados.ibge.gov.br/api/ocorrenciaPesquisa/CD',
            'https://apimetadados.ibge.gov.br/api/ocorrenciaPesquisa/CD/2010/0/0',
            'https://apimetadados.ibge.gov.br/api/ODS/Metas/6',
            'https://apimetadados.ibge.gov.br/api/ODS/FichaMetodologica/6-1-1',
        ];

        foreach ($expected as $url) {
            $http->assertSent(fn (Request $request): bool => $request->url() === $url);
        }
    }
}
