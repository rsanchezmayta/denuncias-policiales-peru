<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denuncia extends Model
{
    public $timestamps = false;

    protected $fillable = ['anio', 'mes', 'distrito_id', 'modalidad_id', 'cantidad'];

    protected $casts = [
        'anio' => 'integer',
        'mes' => 'integer',
        'cantidad' => 'integer',
    ];

    public function scopeFiltrar($query, array $filtros)
    {
        return $query
            ->when($filtros['anio'] ?? null, fn ($q, $v) => $q->where('anio', $v))
            ->when($filtros['mes'] ?? null, fn ($q, $v) => $q->where('mes', $v))
            ->when($filtros['modalidad_id'] ?? null, fn ($q, $v) => $q->where('modalidad_id', $v))
            ->when($filtros['departamento_id'] ?? null, fn ($q, $v) => $q->whereHas(
                'distrito.provincia',
                fn ($sub) => $sub->where('departamento_id', $v)
            ))
            ->when($filtros['distrito'] ?? null, fn ($q, $v) => $q->whereHas(
                'distrito',
                fn ($sub) => $sub->where('nombre', 'like', "%{$v}%")
            ));
    }

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(Distrito::class);
    }

    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(Modalidad::class);
    }
}
