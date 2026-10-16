<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Segmento extends Model
{
    protected $table = 'segmentos';

    protected $fillable = [
        'provincia_id',
        'cluster',
        'total',
        'crecimiento',
        'modalidad_dominante',
        'pct_nacional',
    ];

    protected $casts = [
        'cluster' => 'integer',
        'total' => 'integer',
        'crecimiento' => 'float',
        'pct_nacional' => 'float',
    ];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }
}
