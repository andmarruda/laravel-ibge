# Arquitetura evolutiva

## Direção das dependências

```text
Ibge -> MetadataGateway <- HttpMetadataGateway
                       <- CachedMetadataGateway -> CacheKeyFactory
```

O domínio público depende de contratos próprios, não de rotas do IBGE, drivers de cache ou detalhes do Laravel HTTP Client.

## Estratégia de evolução

### Estágio 1: cliente confiável

- Retornar arrays completos da origem.
- Centralizar endpoints, timeout, retry e exceções.
- Cachear leituras com TTL por tipo de recurso.

### Estágio 2: modelos opt-in

- Adicionar DTOs e collections como uma API paralela.
- Manter os métodos atuais retornando arrays para preservar compatibilidade.
- Medir campos desconhecidos antes de tornar schemas estritos.

### Estágio 3: escala e resiliência

- Decorator com stale-while-revalidate.
- Locks para evitar stampede em chaves populares.
- Métricas de latência, hit rate, erro e idade do dado.
- Pré-aquecimento agendado dos catálogos mais usados.

### Estágio 4: novas fontes ou versões

- Criar outra implementação de `MetadataGateway`.
- Fazer rollout pelo container ou por configuração.
- Manter `Ibge` como API pública estável durante a migração.

## Schema de cache

```text
ibge:metadata:v1:{resource}:{parameters_hash}
```

| Recurso | Parâmetros do hash | TTL padrão |
|---|---|---:|
| `pesquisas` | nenhum (`all`) | 90 dias |
| `ocorrencias` | `codigo_pesquisa` | 90 dias |
| `ocorrencia` | código, ano, mês e ordem | 90 dias |
| `ods_metas` | número do objetivo | 90 dias |
| `ods_ficha_metodologica` | número do indicador | 90 dias |

Os parâmetros são ordenados e serializados antes do SHA-256, garantindo chaves determinísticas. Alterar `schema_version` invalida logicamente todas as entradas antigas sem exigir operações incompatíveis entre drivers de cache.

O `MetadataSynchronizer` ignora leituras existentes e sobrescreve o cache com dados do gateway HTTP. Isso permite executar `ibge:sync` mensalmente sem esperar os 90 dias de validade.

## Sincronização em fila

```text
SyncIbgeMetadata
    -> SyncPesquisaMetadata
        -> SyncPesquisaOccurrence
    -> SyncOdsObjective
        -> SyncOdsIndicator
```

Cada nível primeiro atualiza sua lista e depois enfileira os elementos encontrados. Isso evita um único job de longa duração e permite retry isolado quando um endpoint falhar.

## Persistência relacional

O `DatabaseMetadataStore` faz `upsert` dos recursos sincronizados nas tabelas `ibge_*`. Campos frequentes ficam em colunas próprias e a resposta completa permanece no `payload` JSON, permitindo evolução sem migrations a cada campo novo do IBGE.

Desabilite a persistência mantendo somente o cache:

```dotenv
IBGE_METADATA_DATABASE_ENABLED=false
```

## Decisões

- **Arrays na API base:** toleram campos novos da API externa.
- **Decorator de cache:** permite remover ou trocar a política sem alterar o transporte.
- **Contrato pequeno:** facilita testes, fixtures e fontes alternativas.
- **Sem cache tags:** mantém compatibilidade com todos os stores suportados pelo Laravel.
