<?php

namespace Andmarruda\LaravelIbge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Pesquisa extends Model
{
    protected $table = 'ibge_pesquisas';

    protected $primaryKey = 'codigo';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'synced_at' => 'datetime',
    ];

    public function ocorrencias(): HasMany
    {
        return $this->hasMany(PesquisaOcorrencia::class, 'codigo_pesquisa', 'codigo');
    }
}
