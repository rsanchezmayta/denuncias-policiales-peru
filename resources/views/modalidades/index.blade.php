@extends('layouts.app')

@section('title', 'Modalidades')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Modalidades</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">
                Catálogo de tipos de delito. {{ $modalidades->count() }} modalidades.
            </p>
        </div>
        <a href="{{ route('modalidades.create') }}"
           class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Nueva modalidad
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow mt-6 overflow-hidden dark:border dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-left">
                    <tr>
                        <th class="px-4 py-3">Modalidad</th>
                        <th class="px-4 py-3 text-right">Registros</th>
                        <th class="px-4 py-3 text-right">Total de casos</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($modalidades as $m)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="px-4 py-2 font-medium text-slate-700 dark:text-slate-200">{{ $m->nombre }}</td>
                            <td class="px-4 py-2 text-right text-slate-500">{{ number_format($m->denuncias_count) }}</td>
                            <td class="px-4 py-2 text-right text-slate-700 dark:text-slate-300">{{ number_format($m->total_casos) }}</td>
                            <td class="px-4 py-2">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('modalidades.edit', $m) }}" class="text-sky-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('modalidades.destroy', $m) }}"
                                          onsubmit="return confirm('¿Eliminar la modalidad {{ $m->nombre }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
