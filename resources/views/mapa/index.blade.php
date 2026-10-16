@extends('layouts.app')

@section('title', 'Mapa')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Mapa de denuncias por departamento</h1>
    <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">
        Total acumulado 2018–2026: {{ number_format($totalPais) }} denuncias.
        Tamaño y color indican la intensidad.
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl shadow p-3 dark:border dark:border-slate-800">
            <div id="mapa" class="h-[560px] rounded-lg z-0"></div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-5 dark:border dark:border-slate-800">
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">Ranking</h2>
            <ol class="space-y-2 text-sm">
                @foreach ($datos as $clave => $valor)
                    <li class="flex items-center justify-between gap-3">
                        <span class="text-slate-600 dark:text-slate-300">{{ $loop->iteration }}. {{ $clave }}</span>
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ number_format($valor) }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const datos = @json($datos);
    const ids = @json($ids);
    const max = Math.max(...Object.values(datos));

    const mezcla = (a, b, t) => Math.round(a + (b - a) * t);
    function color(v) {
        const t = Math.sqrt((v || 0) / max);
        return `rgb(${mezcla(224, 3, t)},${mezcla(242, 105, t)},${mezcla(254, 161, t)})`;
    }

    const oscuro = document.documentElement.classList.contains('dark');
    const teselas = oscuro
        ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
        : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';

    const mapa = L.map('mapa', { scrollWheelZoom: false }).setView([-9.19, -75.0], 5);
    L.tileLayer(teselas, { maxZoom: 12, attribution: '&copy; OpenStreetMap &copy; CARTO' }).addTo(mapa);

    fetch('{{ asset('data/peru-departamentos.geojson') }}')
        .then((r) => r.json())
        .then((geo) => {
            L.geoJSON(geo, {
                style: (f) => ({
                    fillColor: color(datos[f.properties.NOMBDEP] || 0),
                    weight: 1,
                    color: 'rgba(255,255,255,.6)',
                    fillOpacity: 0.9,
                }),
                onEachFeature: (f, capa) => {
                    const clave = f.properties.NOMBDEP;
                    const valor = datos[clave] || 0;
                    const id = ids[clave];
                    capa.bindPopup(
                        `<strong>${clave}</strong><br>${valor.toLocaleString('es-PE')} denuncias<br>` +
                        (id ? `<a href="/denuncias?departamento_id=${id}">Ver denuncias</a>` : '')
                    );
                    capa.on('mouseover', () => capa.setStyle({ weight: 2, color: '#38bdf8' }));
                    capa.on('mouseout', () => capa.setStyle({ weight: 1, color: 'rgba(255,255,255,.6)' }));
                },
            }).addTo(mapa);
        });
</script>
@endpush
