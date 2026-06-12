<?php

namespace Andmarruda\LaravelIbge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OdsIndicador extends Model
{
    protected $table = 'ibge_ods_indicadores';

    protected $primaryKey = 'numero';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'ficha_metodologica' => 'array',
        'synced_at' => 'datetime',
    ];

    public function meta(): BelongsTo
    {
        return $this->belongsTo(OdsMeta::class, 'numero_meta', 'numero');
    }
}
