<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Acceso') · Denuncias Policiales Perú</title>

    <script>
        if (localStorage.tema === 'dark' ||
            (!('tema' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Denuncias Policiales Perú</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Panel de analítica · SIDPOL</p>
        </div>

        @yield('content')
    </div>
</body>
</html>
