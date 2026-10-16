<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use Illuminate\View\View;

class MapaController extends Controller
{
    /**
     * El GeoJSON tiene 25 regiones: "LIMA" agrupa Lima Metropolitana y Región Lima,
     * y "CALLAO" corresponde a la Provincia Constitucional.
     */
    private const AGRUPAR = [
        'LIMA METROPOLITANA' => 'LIMA',
        'REGION LIMA' => 'LIMA',
        'PROV. CONST. DEL CALLAO' => 'CALLAO',
    ];

    public function index(): View
    {
        $totales = Denuncia::query()
            ->join('distritos', 'denuncias.distrito_id', '=', 'distritos.id')
            ->join('provincias', 'distritos.provincia_id', '=', 'provincias.id')
            ->join('departamentos', 'provincias.departamento_id', '=', 'departamentos.id')
            ->selectRaw('departamentos.id as id, departamentos.nombre as nombre, SUM(denuncias.cantidad) as total')
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->get();

        $datos = [];
        $ids = [];
        $representativo = [];

        foreach ($totales as $fila) {
            $clave = self::AGRUPAR[$fila->nombre] ?? $fila->nombre;
            $datos[$clave] = ($datos[$clave] ?? 0) + (int) $fila->total;

            if (! isset($representativo[$clave]) || (int) $fila->total > $representativo[$clave]) {
                $representativo[$clave] = (int) $fila->total;
                $ids[$clave] = (int) $fila->id;
            }
        }

        arsort($datos);

        return view('mapa.index', [
            'datos' => $datos,
            'ids' => $ids,
            'totalPais' => array_sum($datos),
        ]);
    }
}
