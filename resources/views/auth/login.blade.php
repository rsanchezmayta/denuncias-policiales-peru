@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <form method="POST" action="{{ route('login') }}"
          class="bg-white dark:bg-slate-900 shadow rounded-xl p-6 space-y-4 dark:border dark:border-slate-800">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Contraseña</label>
            <input id="password" name="password" type="password" required
                   class="mt-1 w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="remember" class="rounded border-slate-300"> Recordarme
        </label>

        @error('email')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <button type="submit"
                class="w-full bg-sky-600 hover:bg-sky-700 text-white font-semibold py-2 rounded-lg transition">
            Entrar
        </button>
    </form>
@endsection
