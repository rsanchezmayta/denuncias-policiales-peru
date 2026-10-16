<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Models\Pronostico;
use Illuminate\View\View;

class PronosticosController extends Controller
{
    public function index(): View
    {
        $historico = Denuncia::query()
            ->selectRaw('anio, mes, SUM(cantidad) as total')
            ->groupBy('anio', 'mes')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        $pronosticos = Pronostico::where('serie', 'total')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        $labels = [];
        $reales = [];
        foreach ($historico as $h) {
            $labels[] = sprintf('%04d-%02d', $h->anio, $h->mes);
            $reales[] = (int) $h->total;
        }

        $nHist = count($reales);
        $pronos = array_fill(0, $nHist, null);
        $inferiores = array_fill(0, $nHist, null);
        $superiores = array_fill(0, $nHist, null);
        $pronos[$nHist - 1] = $reales[$nHist - 1];

        foreach ($pronosticos as $p) {
            $labels[] = sprintf('%04d-%02d', $p->anio, $p->mes);
            $reales[] = null;
            $pronos[] = $p->valor;
            $inferiores[] = $p->limite_inferior;
            $superiores[] = $p->limite_superior;
        }

        return view('pronosticos.index', [
            'labels' => $labels,
            'reales' => $reales,
            'pronos' => $pronos,
            'inferiores' => $inferiores,
            'superiores' => $superiores,
            'pronosticos' => $pronosticos,
            'proximo' => $pronosticos->first(),
            'totalPronosticado' => (int) $pronosticos->sum('valor'),
        ]);
    }
}
