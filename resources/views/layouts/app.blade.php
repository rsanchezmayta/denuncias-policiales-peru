<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') · Denuncias Policiales Perú</title>

    <script>
        if (localStorage.tema === 'dark' ||
            (!('tema' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>[x-cloak] { display: none !important; }</style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3"></script>
    @stack('head')
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-200">
    <nav class="bg-slate-900 text-slate-100 dark:bg-black/40 dark:border-b dark:border-slate-800" x-data="{ abierto: false }">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg">Denuncias Perú</a>

                <div class="hidden md:flex items-center gap-6">
                    @include('layouts._nav_links')
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="alternarTema()" title="Cambiar tema"
                        class="text-slate-300 hover:text-white p-1.5 rounded-lg hover:bg-slate-700/50">
                    <svg class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 1.78a1 1 0 011.42 1.42l-.71.7a1 1 0 11-1.41-1.41l.7-.71zM18 9a1 1 0 110 2h-1a1 1 0 110-2h1zm-1.78 5.22a1 1 0 01-1.42 1.42l-.7-.71a1 1 0 011.41-1.41l.71.7zM10 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-5.22-1.78a1 1 0 01-1.42-1.42l.71-.7a1 1 0 011.41 1.41l-.7.71zM4 11a1 1 0 110-2H3a1 1 0 000 2h1zm.78-6.22a1 1 0 011.42-1.42l.7.71A1 1 0 015.5 5.49l-.72-.71zM10 6a4 4 0 100 8 4 4 0 000-8z"/></svg>
                    <svg class="w-5 h-5 block dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                </button>

                <span class="hidden sm:inline text-sm text-slate-300">{{ auth()->user()?->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-lg">Salir</button>
                </form>

                <button type="button" class="md:hidden text-slate-300 hover:text-white p-1.5" @click="abierto = !abierto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div class="md:hidden border-t border-slate-800 px-4 pb-3 flex flex-col gap-2" x-show="abierto" x-cloak>
            @include('layouts._nav_links')
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-emerald-100 text-emerald-800 px-4 py-2 text-sm dark:bg-emerald-900/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-2 text-sm dark:bg-red-900/40 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        function alternarTema() {
            const raiz = document.documentElement;
            raiz.classList.toggle('dark');
            localStorage.tema = raiz.classList.contains('dark') ? 'dark' : 'light';
        }
    </script>

    @stack('scripts')
</body>
</html>
