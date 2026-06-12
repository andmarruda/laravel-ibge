<?php

namespace Andmarruda\LaravelIbge\Exceptions;

use RuntimeException;
use Throwable;

class IbgeRequestException extends RuntimeException
{
    public static function forEndpoint(string $endpoint, Throwable $previous): self
    {
        return new self(
            sprintf('Falha ao consultar a API de Metadados do IBGE em [%s]: %s', $endpoint, $previous->getMessage()),
            (int) $previous->getCode(),
            $previous,
        );
    }
}
