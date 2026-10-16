<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Segmento;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SegmentosController extends Controller
{
    public const ETIQUETAS = [
        0 => 'Alta incidencia',
        1 => 'Media-alta',
        2 => 'Media-baja',
        3 => 'Baja incidencia',
    ];

    public function index(Request $request): View
    {
        $cluster = $request->filled('cluster') ? (int) $request->input('cluster') : null;
        $departamentoId = $request->filled('departamento_id') ? (int) $request->input('departamento_id') : null;
        $buscar = $request->filled('buscar') ? trim((string) $request->input('buscar')) : null;

        $base = fn () => Segmento::query()
            ->when($cluster !== null, fn ($q) => $q->where('cluster', $cluster))
            ->when($departamentoId, fn ($q) => $q->whereHas(
                'provincia',
                fn ($s) => $s->where('departamento_id', $departamentoId)
            ))
            ->when($buscar, fn ($q) => $q->whereHas(
                'provincia',
                fn ($s) => $s->where('nombre', 'like', "%{$buscar}%")
            ));

        $resumen = $base()
            ->selectRaw('cluster, COUNT(*) as provincias, SUM(total) as total')
            ->groupBy('cluster')
            ->orderBy('cluster')
            ->get();

        $segmentos = $base()
            ->with('provincia.departamento')
            ->orderBy('cluster')
            ->orderByDesc('total')
            ->get();

        return view('segmentos.index', [
            'resumen' => $resumen,
            'segmentos' => $segmentos,
            'departamentos' => Departamento::orderBy('nombre')->get(['id', 'nombre']),
            'etiquetas' => self::ETIQUETAS,
            'cluster' => $cluster,
            'departamentoId' => $departamentoId,
            'buscar' => $buscar,
        ]);
    }
}
