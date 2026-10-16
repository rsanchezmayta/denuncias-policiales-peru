@extends('layouts.app')

@section('title', 'Pronóstico')

@section('content')
    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Pronóstico de denuncias (SARIMA)</h1>
    <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">
        Proyección de los próximos {{ $pronosticos->count() }} meses con el modelo
        SARIMA(1,1,1)(1,1,1,12). Banda sombreada = intervalo de confianza al 80%.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Próximo mes</p>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">
                {{ $proximo ? number_format($proximo->valor) : '—' }}
            </p>
            <p class="text-xs text-slate-400">
                {{ $proximo ? sprintf('%02d/%d', $proximo->mes, $proximo->anio) : '' }}
            </p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total proyectado 12 meses</p>
            <p class="text-2xl font-bold text-sky-600 mt-1">{{ number_format($totalPronosticado) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Precisión del modelo (MAPE)</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">3.5%</p>
            <p class="text-xs text-slate-400">validado con 12 meses de prueba</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 mt-6 dark:border dark:border-slate-800">
        <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Histórico y pronóstico</h2>
        <canvas id="chartPronostico" height="90"></canvas>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow mt-6 overflow-hidden dark:border dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-left">
                    <tr>
                        <th class="px-4 py-3">Mes</th>
                        <th class="px-4 py-3 text-right">Pronóstico</th>
                        <th class="px-4 py-3 text-right">Límite inferior</th>
                        <th class="px-4 py-3 text-right">Límite superior</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($pronosticos as $p)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-4 py-2 font-medium text-slate-700 dark:text-slate-200">{{ sprintf('%02d/%d', $p->mes, $p->anio) }}</td>
                            <td class="px-4 py-2 text-right text-slate-700 dark:text-slate-300">{{ number_format($p->valor) }}</td>
                            <td class="px-4 py-2 text-right text-slate-400">{{ number_format($p->limite_inferior) }}</td>
                            <td class="px-4 py-2 text-right text-slate-400">{{ number_format($p->limite_superior) }}</td>
                        </tr>
                    @endforeach
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

    new Chart(document.getElementById('chartPronostico'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                { label: 'Histórico', data: @json($reales), borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,.10)', fill: true, pointRadius: 0, tension: .3 },
                { label: 'Intervalo superior', data: @json($superiores), borderColor: 'transparent', backgroundColor: 'rgba(245,158,11,.15)', pointRadius: 0, fill: { target: 2 } },
                { label: 'Intervalo inferior', data: @json($inferiores), borderColor: 'transparent', pointRadius: 0, fill: false },
                { label: 'Pronóstico', data: @json($pronos), borderColor: '#f59e0b', borderDash: [6, 4], backgroundColor: 'transparent', pointRadius: 2, tension: .3 }
            ]
        },
        options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: false } } }
    });
</script>
@endpush
