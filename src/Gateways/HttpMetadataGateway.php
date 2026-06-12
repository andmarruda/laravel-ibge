<?php

namespace Andmarruda\LaravelIbge\Gateways;

use Andmarruda\LaravelIbge\Contracts\MetadataGateway;
use Andmarruda\LaravelIbge\Exceptions\IbgeRequestException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Throwable;

final class HttpMetadataGateway implements MetadataGateway
{
    public function __construct(
        private readonly Factory $http,
        private readonly string $baseUrl,
        private readonly int $timeout,
        private readonly int $connectTimeout,
        private readonly int $retryTimes,
        private readonly int $retrySleepMs,
    ) {
    }

    public function pesquisas(): array
    {
        return $this->get('Pesquisa');
    }

    public function ocorrencias(string $codigoPesquisa): array
    {
        return $this->get('ocorrenciaPesquisa/'.rawurlencode($codigoPesquisa));
    }

    public function ocorrencia(string $codigoPesquisa, int $ano, int $mes = 0, int $ordemPeriodo = 0): array
    {
        return $this->get(sprintf(
            'ocorrenciaPesquisa/%s/%d/%d/%d',
            rawurlencode($codigoPesquisa),
            $ano,
            $mes,
            $ordemPeriodo,
        ));
    }

    public function metas(int $numeroObjetivo): array
    {
        return $this->get('ODS/Metas/'.$numeroObjetivo);
    }

    public function fichaMetodologica(string $numeroIndicador): array
    {
        return $this->get('ODS/FichaMetodologica/'.rawurlencode($numeroIndicador));
    }

    private function get(string $endpoint): array
    {
        try {
            return $this->request()
                ->get($endpoint)
                ->throw()
                ->json();
        } catch (Throwable $exception) {
            throw IbgeRequestException::forEndpoint($endpoint, $exception);
        }
    }

    private function request(): PendingRequest
    {
        return $this->http
            ->baseUrl(rtrim($this->baseUrl, '/').'/')
            ->acceptJson()
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->retry($this->retryTimes, $this->retrySleepMs);
    }
}
