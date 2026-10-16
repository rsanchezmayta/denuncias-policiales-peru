<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Segmento;
use Illuminate\Http\JsonResponse;

class SegmentoApiController extends Controller
{
    public function index(): JsonResponse
    {
        $segmentos = Segmento::query()
            ->with('provincia.departamento')
            ->orderBy('cluster')
            ->orderByDesc('total')
            ->get()
            ->map(fn (Segmento $s) => [
                'provincia' => $s->provincia?->nombre,
                'departamento' => $s->provincia?->departamento?->nombre,
                'cluster' => $s->cluster,
                'total' => $s->total,
                'crecimiento' => $s->crecimiento,
                'modalidad_dominante' => $s->modalidad_dominante,
                'pct_nacional' => $s->pct_nacional,
            ]);

        return response()->json(['data' => $segmentos]);
    }
}
