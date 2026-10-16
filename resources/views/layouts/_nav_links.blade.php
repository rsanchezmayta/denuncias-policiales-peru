@php
    $enlace = fn (string $ruta, string $patron) => 'text-sm '.(
        request()->routeIs($patron) ? 'text-white font-semibold' : 'text-slate-300 hover:text-white'
    );
@endphp

<a href="{{ route('dashboard') }}" class="{{ $enlace('dashboard', 'dashboard') }}">Dashboard</a>

@if (Route::has('denuncias.index'))
    <a href="{{ route('denuncias.index') }}" class="{{ $enlace('denuncias', 'denuncias.*') }}">Denuncias</a>
@endif

@if (Route::has('segmentos.index'))
    <a href="{{ route('segmentos.index') }}" class="{{ $enlace('segmentos', 'segmentos.*') }}">Segmentos</a>
@endif

@if (Route::has('pronosticos.index'))
    <a href="{{ route('pronosticos.index') }}" class="{{ $enlace('pronosticos', 'pronosticos.*') }}">Pronóstico</a>
@endif

@if (Route::has('mapa.index'))
    <a href="{{ route('mapa.index') }}" class="{{ $enlace('mapa', 'mapa.*') }}">Mapa</a>
@endif

@if (Route::has('modalidades.index'))
    <a href="{{ route('modalidades.index') }}" class="{{ $enlace('modalidades', 'modalidades.*') }}">Modalidades</a>
@endif
