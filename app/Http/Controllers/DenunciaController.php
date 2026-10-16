<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Support\Filtros;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DenunciaController extends Controller
{
    private const ORDENABLES = ['id', 'anio', 'mes', 'cantidad'];

    public function index(Request $request): View
    {
        $filtros = Filtros::desdeRequest($request);
        $orden = in_array($request->input('orden'), self::ORDENABLES, true) ? $request->input('orden') : 'id';
        $dir = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        $denuncias = Denuncia::query()
            ->filtrar($filtros)
            ->with(['distrito.provincia.departamento', 'modalidad'])
            ->orderBy($orden, $dir)
            ->paginate(25)
            ->withQueryString();

        return view('denuncias.index', array_merge(Filtros::opciones(), [
            'filtros' => $filtros,
            'denuncias' => $denuncias,
            'orden' => $orden,
            'dir' => $dir,
        ]));
    }

    public function show(Denuncia $denuncia): View
    {
        $denuncia->load(['distrito.provincia.departamento', 'modalidad']);

        $serie = Denuncia::query()
            ->where('distrito_id', $denuncia->distrito_id)
            ->where('modalidad_id', $denuncia->modalidad_id)
            ->orderBy('anio')
            ->orderBy('mes')
            ->get(['anio', 'mes', 'cantidad']);

        return view('denuncias.show', [
            'denuncia' => $denuncia,
            'serie' => $serie,
            'totalSerie' => (int) $serie->sum('cantidad'),
        ]);
    }

    public function exportar(Request $request): StreamedResponse
    {
        $filtros = Filtros::desdeRequest($request);

        return response()->streamDownload(function () use ($filtros) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['anio', 'mes', 'departamento', 'provincia', 'distrito', 'modalidad', 'cantidad']);

            Denuncia::query()
                ->filtrar($filtros)
                ->with(['distrito.provincia.departamento', 'modalidad'])
                ->orderBy('anio')
                ->orderBy('mes')
                ->chunk(2000, function ($filas) use ($salida) {
                    foreach ($filas as $d) {
                        fputcsv($salida, [
                            $d->anio,
                            $d->mes,
                            $d->distrito->provincia->departamento->nombre,
                            $d->distrito->provincia->nombre,
                            $d->distrito->nombre,
                            $d->modalidad->nombre,
                            $d->cantidad,
                        ]);
                    }
                });

            fclose($salida);
        }, 'denuncias-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
