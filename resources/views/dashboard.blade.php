@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Panorama de denuncias policiales</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Fuente: SIDPOL – MININTER · Datos abiertos del Perú</p>
        </div>
    </div>

    <div class="mt-6">
        <x-filtros :filtros="$filtros" :anios="$anios" :departamentos="$departamentos" :modalidades="$modalidades"
                   :exportar-ruta="route('denuncias.exportar', request()->query())" />
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total de denuncias</p>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ number_format($total) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Departamento con más casos</p>
            <p class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $deptoTop?->nombre ?? '—' }}</p>
            <p class="text-xs text-slate-400">{{ number_format($deptoTop?->total ?? 0) }} denuncias</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Modalidad más frecuente</p>
            <p class="text-base font-bold text-slate-800 dark:text-slate-100 mt-1 leading-tight">{{ $modalidadTop?->nombre ?? '—' }}</p>
            <p class="text-xs text-slate-400">{{ number_format($modalidadTop?->total ?? 0) }} denuncias</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Variación último año cerrado</p>
            @if (is_null($variacion))
                <p class="text-2xl font-bold text-slate-400 mt-1">—</p>
            @else
                <p class="text-2xl font-bold mt-1 {{ $variacion >= 0 ? 'text-red-600' : 'text-emerald-600' }}">
                    {{ $variacion >= 0 ? '+' : '' }}{{ $variacion }}%
                </p>
            @endif
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 lg:col-span-2 dark:border dark:border-slate-800">
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Evolución anual de denuncias</h2>
            <canvas id="chartAnio" height="80"></canvas>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Top 10 departamentos</h2>
            <canvas id="chartDeptos" height="260"></canvas>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Distribución por modalidad</h2>
            <canvas id="chartModalidad" height="260"></canvas>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 lg:col-span-2 dark:border dark:border-slate-800">
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Estacionalidad: denuncias por mes</h2>
            <canvas id="chartMes" height="70"></canvas>
        </div>
    </div>

    <p class="text-xs text-slate-400 mt-3">Consejo: haz clic en una barra, punto o porción para filtrar el tablero.</p>
@endsection

@push('scripts')
<script>
    const palette = ['#0ea5e9','#6366f1','#f59e0b','#ef4444','#10b981','#8b5cf6','#ec4899'];

    if (document.documentElement.classList.contains('dark')) {
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = 'rgba(148,163,184,.12)';
    }

    function aplicarFiltro(clave, valor) {
        const url = new URL(window.location);
        url.searchParams.set(clave, valor);
        window.location = url;
    }

    const idsDeptos = @json($topDeptos->pluck('id'));
    const idsModalidades = @json($porModalidad->pluck('id'));
    const anios = @json($porAnio->keys());
    const meses = @json($porMes->keys());

    new Chart(document.getElementById('chartAnio'), {
        type: 'line',
        data: {
            labels: anios,
            datasets: [{
                label: 'Denuncias',
                data: @json($porAnio->values()),
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14,165,233,.15)',
                fill: true, tension: .3
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
            onClick: (e, el) => { if (el.length) aplicarFiltro('anio', anios[el[0].index]); }
        }
    });

    new Chart(document.getElementById('chartDeptos'), {
        type: 'bar',
        data: {
            labels: @json($topDeptos->pluck('nombre')),
            datasets: [{ label: 'Denuncias', data: @json($topDeptos->pluck('total')), backgroundColor: '#6366f1' }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } },
            onClick: (e, el) => { if (el.length) aplicarFiltro('departamento_id', idsDeptos[el[0].index]); }
        }
    });

    new Chart(document.getElementById('chartModalidad'), {
        type: 'doughnut',
        data: {
            labels: @json($porModalidad->pluck('nombre')),
            datasets: [{ data: @json($porModalidad->pluck('total')), backgroundColor: palette }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } },
            onClick: (e, el) => { if (el.length) aplicarFiltro('modalidad_id', idsModalidades[el[0].index]); }
        }
    });

    new Chart(document.getElementById('chartMes'), {
        type: 'bar',
        data: {
            labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Set','Oct','Nov','Dic'],
            datasets: [{ label: 'Denuncias', data: @json($porMes->values()), backgroundColor: '#f59e0b' }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
            onClick: (e, el) => { if (el.length) aplicarFiltro('mes', meses[el[0].index]); }
        }
    });
</script>
@endpush
