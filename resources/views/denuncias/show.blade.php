@extends('layouts.app')

@section('title', 'Detalle de denuncia')

@section('content')
    <a href="{{ url()->previous() }}" class="text-sm text-sky-600 hover:underline">&larr; Volver</a>

    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-2">
        {{ $denuncia->modalidad->nombre }}
    </h1>
    <p class="text-slate-500 dark:text-slate-400 text-sm">
        {{ $denuncia->distrito->nombre }} ·
        {{ $denuncia->distrito->provincia->nombre }} ·
        {{ $denuncia->distrito->provincia->departamento->nombre }}
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Periodo</p>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">
                {{ ucfirst(\Carbon\Carbon::create()->month($denuncia->mes)->translatedFormat('F')) }} {{ $denuncia->anio }}
            </p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Denuncias ese mes</p>
            <p class="text-2xl font-bold text-sky-600 mt-1">{{ number_format($denuncia->cantidad) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total histórico (misma modalidad)</p>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ number_format($totalSerie) }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 mt-6 dark:border dark:border-slate-800">
        <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">
            Serie histórica · {{ $denuncia->distrito->nombre }}
        </h2>
        <canvas id="chartSerie" height="80"></canvas>
    </div>
@endsection

@push('scripts')
<script>
    if (document.documentElement.classList.contains('dark')) {
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = 'rgba(148,163,184,.12)';
    }

    new Chart(document.getElementById('chartSerie'), {
        type: 'line',
        data: {
            labels: @json($serie->map(fn ($s) => sprintf('%04d-%02d', $s->anio, $s->mes))),
            datasets: [{
                label: 'Denuncias',
                data: @json($serie->pluck('cantidad')),
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14,165,233,.15)',
                fill: true, tension: .3, pointRadius: 2
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
</script>
@endpush
