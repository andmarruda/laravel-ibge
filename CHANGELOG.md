# Changelog

## [0.1.0] - 2026-06-12

- Cliente para pesquisas, ocorrências e metadados ODS do IBGE.
- Cache versionado com validade padrão de 90 dias.
- Sincronização mensal distribuída em jobs de fila.
- Comando Artisan `ibge:sync`.
- Persistência relacional com migrations publicáveis e modelos Eloquent.
- Compatibilidade com Laravel 10, 11 e 12.
