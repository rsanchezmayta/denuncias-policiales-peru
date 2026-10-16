@extends('layouts.app')

@section('title', 'Denuncias')

@section('content')
    @php
        $ordenar = function (string $campo) use ($orden, $dir) {
            $nuevo = ($orden === $campo && $dir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['orden' => $campo, 'dir' => $nuevo]);
        };
        $flecha = fn (string $campo) => $orden === $campo ? ($dir === 'asc' ? '▲' : '▼') : '';
    @endphp

    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Explorador de denuncias</h1>
    <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">
        {{ number_format($denuncias->total()) }} registros encontrados ({{ number_format($denuncias->sum('cantidad')) }} denuncias en esta página).
    </p>

    <div class="mt-6">
        <x-filtros :filtros="$filtros" :anios="$anios" :departamentos="$departamentos" :modalidades="$modalidades"
                   :meses="$meses" :distrito="true"
                   :exportar-ruta="route('denuncias.exportar', request()->query())" />
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow mt-6 overflow-hidden dark:border dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-left">
                    <tr>
                        <th class="px-4 py-3"><a href="{{ $ordenar('anio') }}" class="hover:text-sky-600">Año {{ $flecha('anio') }}</a></th>
                        <th class="px-4 py-3"><a href="{{ $ordenar('mes') }}" class="hover:text-sky-600">Mes {{ $flecha('mes') }}</a></th>
                        <th class="px-4 py-3">Departamento</th>
                        <th class="px-4 py-3">Provincia</th>
                        <th class="px-4 py-3">Distrito</th>
                        <th class="px-4 py-3">Modalidad</th>
                        <th class="px-4 py-3 text-right"><a href="{{ $ordenar('cantidad') }}" class="hover:text-sky-600">Cantidad {{ $flecha('cantidad') }}</a></th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($denuncias as $d)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-4 py-2 text-slate-700 dark:text-slate-300">{{ $d->anio }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ ucfirst(\Carbon\Carbon::create()->month($d->mes)->translatedFormat('M')) }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ $d->distrito->provincia->departamento->nombre }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ $d->distrito->provincia->nombre }}</td>
                            <td class="px-4 py-2 font-medium text-slate-700 dark:text-slate-200">{{ $d->distrito->nombre }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ $d->modalidad->nombre }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-slate-700 dark:text-slate-200">{{ number_format($d->cantidad) }}</td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('denuncias.show', $d) }}" class="text-sky-600 hover:underline">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-400">
                                No hay denuncias para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $denuncias->links() }}
    </div>
@endsection
