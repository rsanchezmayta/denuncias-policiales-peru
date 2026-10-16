<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Support\Filtros;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = Filtros::desdeRequest($request);
        $base = fn () => Denuncia::query()->filtrar($filtros);

        $total = (int) $base()->sum('cantidad');

        $porAnio = $base()
            ->selectRaw('anio, SUM(cantidad) as total')
            ->groupBy('anio')
            ->orderBy('anio')
            ->pluck('total', 'anio');

        $topDeptos = $base()
            ->join('distritos', 'denuncias.distrito_id', '=', 'distritos.id')
            ->join('provincias', 'distritos.provincia_id', '=', 'provincias.id')
            ->join('departamentos', 'provincias.departamento_id', '=', 'departamentos.id')
            ->selectRaw('departamentos.id as id, departamentos.nombre as nombre, SUM(denuncias.cantidad) as total')
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $porModalidad = $base()
            ->join('modalidades', 'denuncias.modalidad_id', '=', 'modalidades.id')
            ->selectRaw('modalidades.id as id, modalidades.nombre as nombre, SUM(denuncias.cantidad) as total')
            ->groupBy('modalidades.id', 'modalidades.nombre')
            ->orderByDesc('total')
            ->get();

        $porMes = $base()
            ->selectRaw('mes, SUM(cantidad) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        return view('dashboard', array_merge(Filtros::opciones(), [
            'filtros' => $filtros,
            'total' => $total,
            'porAnio' => $porAnio,
            'topDeptos' => $topDeptos,
            'deptoTop' => $topDeptos->first(),
            'porModalidad' => $porModalidad,
            'modalidadTop' => $porModalidad->first(),
            'porMes' => $porMes,
            'variacion' => $this->variacionAnual($porAnio),
        ]));
    }

    private function variacionAnual(Collection $porAnio): ?float
    {
        $completos = $porAnio->keys()
            ->map(fn ($anio) => (int) $anio)
            ->filter(fn ($anio) => $anio < now()->year)
            ->sort()
            ->values();

        if ($completos->count() < 2) {
            return null;
        }

        $anterior = (int) $porAnio[$completos->slice(-2, 1)->first()];
        $actual = (int) $porAnio[$completos->last()];

        return $anterior > 0 ? round((($actual - $anterior) / $anterior) * 100, 1) : null;
    }
}
