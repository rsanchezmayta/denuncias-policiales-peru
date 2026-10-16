<?php

namespace App\Support;

use App\Models\Denuncia;
use App\Models\Departamento;
use App\Models\Modalidad;
use Illuminate\Http\Request;

class Filtros
{
    public static function desdeRequest(Request $request): array
    {
        return [
            'anio' => $request->filled('anio') ? (int) $request->input('anio') : null,
            'mes' => $request->filled('mes') ? (int) $request->input('mes') : null,
            'departamento_id' => $request->filled('departamento_id') ? (int) $request->input('departamento_id') : null,
            'modalidad_id' => $request->filled('modalidad_id') ? (int) $request->input('modalidad_id') : null,
            'distrito' => $request->filled('distrito') ? trim((string) $request->input('distrito')) : null,
        ];
    }

    public static function activos(array $filtros): array
    {
        return array_filter($filtros, fn ($valor) => $valor !== null && $valor !== '');
    }

    public static function opciones(): array
    {
        return [
            'anios' => Denuncia::query()->distinct()->orderBy('anio')->pluck('anio'),
            'meses' => range(1, 12),
            'departamentos' => Departamento::orderBy('nombre')->get(['id', 'nombre']),
            'modalidades' => Modalidad::orderBy('nombre')->get(['id', 'nombre']),
        ];
    }
}
