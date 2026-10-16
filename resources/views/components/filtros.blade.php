@props([
    'filtros' => [],
    'anios' => [],
    'departamentos' => [],
    'modalidades' => [],
    'meses' => null,
    'distrito' => false,
    'exportarRuta' => null,
])

@php
    $campo = 'rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500';
    $etiqueta = 'block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1';
@endphp

<form method="GET" action="{{ url()->current() }}"
      class="bg-white dark:bg-slate-900 rounded-xl shadow p-4 dark:border dark:border-slate-800">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label class="{{ $etiqueta }}">Año</label>
            <select name="anio" class="{{ $campo }} w-full">
                <option value="">Todos</option>
                @foreach ($anios as $anio)
                    <option value="{{ $anio }}" @selected(($filtros['anio'] ?? null) == $anio)>{{ $anio }}</option>
                @endforeach
            </select>
        </div>

        @if ($meses)
            <div>
                <label class="{{ $etiqueta }}">Mes</label>
                <select name="mes" class="{{ $campo }} w-full">
                    <option value="">Todos</option>
                    @foreach ($meses as $mes)
                        <option value="{{ $mes }}" @selected(($filtros['mes'] ?? null) == $mes)>{{ ucfirst(\Carbon\Carbon::create()->month($mes)->translatedFormat('F')) }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <label class="{{ $etiqueta }}">Departamento</label>
            <select name="departamento_id" class="{{ $campo }} w-full">
                <option value="">Todos</option>
                @foreach ($departamentos as $dep)
                    <option value="{{ $dep->id }}" @selected(($filtros['departamento_id'] ?? null) == $dep->id)>{{ $dep->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="{{ $etiqueta }}">Modalidad</label>
            <select name="modalidad_id" class="{{ $campo }} w-full">
                <option value="">Todas</option>
                @foreach ($modalidades as $mod)
                    <option value="{{ $mod->id }}" @selected(($filtros['modalidad_id'] ?? null) == $mod->id)>{{ $mod->nombre }}</option>
                @endforeach
            </select>
        </div>

        @if ($distrito)
            <div>
                <label class="{{ $etiqueta }}">Distrito (texto)</label>
                <input type="text" name="distrito" value="{{ $filtros['distrito'] ?? '' }}" placeholder="Ej: MIRAFLORES"
                       class="{{ $campo }} w-full">
            </div>
        @endif
    </div>

    <div class="flex items-center gap-2 mt-4">
        <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Filtrar
        </button>
        <a href="{{ url()->current() }}"
           class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 px-2 py-2">
            Limpiar
        </a>

        @if ($exportarRuta)
            <a href="{{ $exportarRuta }}"
               class="ml-auto text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg">
                Exportar CSV
            </a>
        @endif
    </div>
</form>
