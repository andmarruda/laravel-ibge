<?php

namespace Andmarruda\LaravelIbge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PesquisaOcorrencia extends Model
{
    protected $table = 'ibge_pesquisa_ocorrencias';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'synced_at' => 'datetime',
    ];

    public function pesquisa(): BelongsTo
    {
        return $this->belongsTo(Pesquisa::class, 'codigo_pesquisa', 'codigo');
    }
}
