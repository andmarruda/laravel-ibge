<?php

namespace Andmarruda\LaravelIbge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class OdsMeta extends Model
{
    protected $table = 'ibge_ods_metas';

    protected $primaryKey = 'numero';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'synced_at' => 'datetime',
    ];

    public function indicadores(): HasMany
    {
        return $this->hasMany(OdsIndicador::class, 'numero_meta', 'numero');
    }
}
