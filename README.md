# Laravel IBGE

Package Laravel para consumir e sincronizar dados da [API de Metadados do IBGE](https://apimetadados.ibge.gov.br/).

Inclui cache configurável, persistência em banco de dados, migrations publicáveis, modelos Eloquent e jobs para atualização mensal.

## Instalação via Composer

Instale o package diretamente pelo Composer:

```bash
composer require andmarruda/laravel-ibge
```

O Laravel descobre automaticamente o service provider e a facade do package.

Para publicar a configuração:

```bash
php artisan vendor:publish --tag=ibge-config
```

As migrations são carregadas automaticamente. Para publicá-las no projeto e poder customizá-las:

```bash
php artisan vendor:publish --tag=ibge-migrations
php artisan migrate
```

Caso não queira customizar as migrations, basta executar:

```bash
php artisan migrate
```

## Início rápido

Consulte a API usando a facade:

```php
use Andmarruda\LaravelIbge\Facades\Ibge;

$pesquisas = Ibge::pesquisas();
$ocorrencias = Ibge::ocorrencias('CD');
$censo2010 = Ibge::ocorrencia('CD', 2010);
$metas = Ibge::metas(6);
$ficha = Ibge::fichaMetodologica('6-1-1');
```

## Uso

```php
use Andmarruda\LaravelIbge\Ibge;
use Andmarruda\LaravelIbge\Facades\Ibge as IbgeFacade;

$ibge = app(Ibge::class);

$pesquisas = $ibge->pesquisas();
$ocorrencias = $ibge->ocorrencias('CD');
$censo2010 = $ibge->ocorrencia('CD', 2010);
$metas = $ibge->metas(6);
$ficha = $ibge->fichaMetodologica('6-1-1');

$pesquisas = IbgeFacade::pesquisas();
```

Os resultados são arrays iguais aos retornados pelo IBGE. Assim, novos campos adicionados pela API ficam imediatamente disponíveis sem exigir uma nova versão do package.

## Endpoints mapeados

| Método do package | Endpoint IBGE |
|---|---|
| `pesquisas()` | `GET /api/Pesquisa` |
| `ocorrencias($codigo)` | `GET /api/ocorrenciaPesquisa/{codigo}` |
| `ocorrencia($codigo, $ano, $mes, $ordem)` | `GET /api/ocorrenciaPesquisa/{codigo}/{ano}/{mes}/{ordem}` |
| `metas($objetivo)` | `GET /api/ODS/Metas/{objetivo}` |
| `fichaMetodologica($indicador)` | `GET /api/ODS/FichaMetodologica/{indicador}` |

## Cache

O cache é aplicado pelo decorator `CachedMetadataGateway`, sem acoplar essa responsabilidade ao cliente HTTP.

Formato das chaves:

```text
{prefix}:{schema_version}:{resource}:{sha256_dos_parametros}
```

Exemplos:

```text
ibge:metadata:v1:pesquisas:all
ibge:metadata:v1:ocorrencias:2f...
ibge:metadata:v1:ods_metas:9a...
```

O `schema_version` permite invalidar todo o formato anterior quando a estrutura interna do cache mudar. Os TTLs são separados por recurso em `config/ibge.php`, pois catálogos e ocorrências podem ter ritmos de atualização diferentes.

Variáveis disponíveis:

```dotenv
IBGE_METADATA_BASE_URL=https://apimetadados.ibge.gov.br/api
IBGE_METADATA_CACHE_ENABLED=true
IBGE_METADATA_CACHE_STORE=redis
IBGE_METADATA_CACHE_PREFIX=ibge:metadata
IBGE_METADATA_CACHE_TTL_PESQUISAS=7776000
IBGE_METADATA_CACHE_TTL_OCORRENCIAS=7776000
IBGE_METADATA_CACHE_TTL_OCORRENCIA=7776000
IBGE_METADATA_CACHE_TTL_ODS_METAS=7776000
IBGE_METADATA_CACHE_TTL_ODS_FICHA=7776000
```

O padrão é de `7.776.000` segundos, equivalente a 90 dias. A sincronização mensal força a substituição dos valores mesmo quando o cache ainda está válido.

## Sincronização mensal

O comando abaixo enfileira a atualização completa:

```bash
php artisan ibge:sync
```

Fluxo dos jobs:

```text
SyncIbgeMetadata
    -> SyncPesquisaMetadata (uma por pesquisa)
        -> SyncPesquisaOccurrence (uma por ocorrência)
    -> SyncOdsObjective (objetivos 1 a 17)
        -> SyncOdsIndicator (um por indicador)
```

Configure um worker de filas e agende o comando mensalmente:

```bash
php artisan queue:work --queue=ibge,default
```

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('ibge:sync')
    ->monthly()
    ->withoutOverlapping();
```

Fila e conexão podem ser configuradas:

```dotenv
IBGE_METADATA_SYNC_CONNECTION=redis
IBGE_METADATA_SYNC_QUEUE=ibge
IBGE_METADATA_DATABASE_ENABLED=true
IBGE_METADATA_DATABASE_CONNECTION=mysql
```

## Banco de dados

A sincronização salva os dados em quatro tabelas:

| Tabela | Conteúdo |
|---|---|
| `ibge_pesquisas` | Catálogo de pesquisas |
| `ibge_pesquisa_ocorrencias` | Lista e detalhes das ocorrências |
| `ibge_ods_metas` | Metas dos objetivos ODS |
| `ibge_ods_indicadores` | Indicadores e fichas metodológicas |

Cada tabela mantém campos úteis para consulta e um `payload` JSON com a resposta completa da API.

Modelos Eloquent disponíveis:

```php
use Andmarruda\LaravelIbge\Models\OdsIndicador;
use Andmarruda\LaravelIbge\Models\OdsMeta;
use Andmarruda\LaravelIbge\Models\Pesquisa;
use Andmarruda\LaravelIbge\Models\PesquisaOcorrencia;

$pesquisas = Pesquisa::with('ocorrencias')->get();
$indicadores = OdsIndicador::with('meta')->get();
```

## Arquitetura evolutiva

```text
Aplicação Laravel
    -> Ibge (API pública estável)
        -> MetadataGateway (contrato)
            -> CachedMetadataGateway (decorator opcional)
                -> HttpMetadataGateway (API externa)
```

- `Ibge` mantém a API pública do package pequena e estável.
- `MetadataGateway` é o ponto de substituição para uma API v2, fixtures ou outra fonte.
- `HttpMetadataGateway` concentra rotas, retry, timeout e tradução de erros.
- `CachedMetadataGateway` pode evoluir independentemente para stale-while-revalidate ou locks.
- `CacheKeyFactory` versiona o schema e pode ser substituído por aplicações consumidoras.
- Respostas brutas evitam perda de dados quando o IBGE acrescentar campos. DTOs tipados podem ser adicionados no futuro como uma API opt-in.

As etapas de evolução, decisões e o schema completo estão em [docs/architecture.md](docs/architecture.md).

Para substituir a fonte de dados, registre outra implementação no container:

```php
$this->app->singleton(
    \Andmarruda\LaravelIbge\Contracts\MetadataGateway::class,
    MinhaFonteDeMetadados::class,
);
```

## Testes

```bash
composer test
```

[![Buy Me a Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-support-yellow?logo=buy-me-a-coffee&logoColor=white)](https://buymeacoffee.com/andmarruda)
