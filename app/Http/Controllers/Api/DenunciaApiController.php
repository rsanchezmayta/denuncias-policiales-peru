<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DenunciaResource;
use App\Models\Denuncia;
use App\Support\Filtros;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DenunciaApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filtros = Filtros::desdeRequest($request);
        $porPagina = min(max((int) $request->input('per_page', 50), 1), 200);

        $denuncias = Denuncia::query()
            ->filtrar($filtros)
            ->with(['distrito.provincia.departamento', 'modalidad'])
            ->orderBy('anio')
            ->orderBy('mes')
            ->paginate($porPagina);

        return DenunciaResource::collection($denuncias);
    }

    public function resumen(Request $request): JsonResponse
    {
        $filtros = Filtros::desdeRequest($request);
        $base = fn () => Denuncia::query()->filtrar($filtros);

        return response()->json([
            'filtros' => array_filter($filtros, fn ($v) => $v !== null && $v !== ''),
            'total' => (int) $base()->sum('cantidad'),
            'por_anio' => $base()
                ->selectRaw('anio, SUM(cantidad) as total')
                ->groupBy('anio')->orderBy('anio')->pluck('total', 'anio'),
            'por_modalidad' => $base()
                ->join('modalidades', 'denuncias.modalidad_id', '=', 'modalidades.id')
                ->selectRaw('modalidades.nombre as modalidad, SUM(denuncias.cantidad) as total')
                ->groupBy('modalidades.nombre')->orderByDesc('total')->get(),
        ]);
    }
}
