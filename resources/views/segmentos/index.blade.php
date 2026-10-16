@extends('layouts.app')

@section('title', 'Segmentos')

@section('content')
    @php
        $campo = 'rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500';
        $etiqueta = 'block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1';
    @endphp

    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Segmentación de provincias (K-means)</h1>
    <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">
        Provincias agrupadas en 4 segmentos según la intensidad de denuncias.
    </p>

    <form method="GET" class="bg-white dark:bg-slate-900 rounded-xl shadow p-4 mt-6 dark:border dark:border-slate-800">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="{{ $etiqueta }}">Segmento</label>
                <select name="cluster" class="{{ $campo }} w-full">
                    <option value="">Todos</option>
                    @foreach ($etiquetas as $id => $nombre)
                        <option value="{{ $id }}" @selected($cluster === $id)>{{ $id }} · {{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $etiqueta }}">Departamento</label>
                <select name="departamento_id" class="{{ $campo }} w-full">
                    <option value="">Todos</option>
                    @foreach ($departamentos as $dep)
                        <option value="{{ $dep->id }}" @selected($departamentoId === $dep->id)>{{ $dep->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $etiqueta }}">Provincia (texto)</label>
                <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Ej: AREQUIPA" class="{{ $campo }} w-full">
            </div>
        </div>
        <div class="flex items-center gap-2 mt-4">
            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Filtrar</button>
            <a href="{{ url()->current() }}" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 px-2 py-2">Limpiar</a>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        @foreach ($resumen as $fila)
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 border-l-4 border-sky-500 dark:border dark:border-slate-800 dark:border-l-4 dark:border-l-sky-500">
                <p class="text-xs uppercase tracking-wide text-slate-400">Segmento {{ $fila->cluster }}</p>
                <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $etiquetas[$fila->cluster] ?? '—' }}</p>
                <p class="text-2xl font-bold text-sky-600 mt-2">{{ number_format($fila->total) }}</p>
                <p class="text-xs text-slate-400">{{ $fila->provincias }} provincias</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Provincias por segmento</h2>
            <canvas id="chartProvincias" height="200"></canvas>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Reparto del total de denuncias</h2>
            <canvas id="chartTotales" height="200"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow mt-6 overflow-hidden dark:border dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-left">
                    <tr>
                        <th class="px-4 py-3">Provincia</th>
                        <th class="px-4 py-3">Departamento</th>
                        <th class="px-4 py-3 text-center">Segmento</th>
                        <th class="px-4 py-3 text-right">Denuncias</th>
                        <th class="px-4 py-3 text-right">Crecimiento</th>
                        <th class="px-4 py-3">Modalidad dominante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($segmentos as $s)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-4 py-2 font-medium text-slate-700 dark:text-slate-200">{{ $s->provincia->nombre }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ $s->provincia->departamento->nombre }}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">{{ $s->cluster }}</span>
                            </td>
                            <td class="px-4 py-2 text-right text-slate-700 dark:text-slate-300">{{ number_format($s->total) }}</td>
                            <td class="px-4 py-2 text-right text-slate-500">{{ number_format($s->crecimiento, 2) }}x</td>
                            <td class="px-4 py-2 text-slate-500">{{ $s->modalidad_dominante }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">Sin provincias para los filtros seleccionados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    if (document.documentElement.classList.contains('dark')) {
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = 'rgba(148,163,184,.12)';
    }

    const etiquetas = @json(array_values($etiquetas));

    new Chart(document.getElementById('chartProvincias'), {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: [{ label: 'Provincias', data: @json($resumen->pluck('provincias')), backgroundColor: ['#0ea5e9','#6366f1','#f59e0b','#10b981'] }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('chartTotales'), {
        type: 'doughnut',
        data: {
            labels: etiquetas,
            datasets: [{ data: @json($resumen->pluck('total')), backgroundColor: ['#0ea5e9','#6366f1','#f59e0b','#10b981'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush
