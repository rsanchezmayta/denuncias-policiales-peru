@csrf

<div>
    <label for="nombre" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nombre</label>
    <input id="nombre" name="nombre" type="text" required value="{{ old('nombre', $modalidad->nombre) }}"
           class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
    @error('nombre')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center gap-3 pt-2">
    <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Guardar
    </button>
    <a href="{{ route('modalidades.index') }}" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200">
        Cancelar
    </a>
</div>
