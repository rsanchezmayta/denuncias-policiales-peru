<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DenunciaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\ModalidadController;
use App\Http\Controllers\PronosticosController;
use App\Http\Controllers\SegmentosController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/denuncias', [DenunciaController::class, 'index'])->name('denuncias.index');
    Route::get('/denuncias/exportar', [DenunciaController::class, 'exportar'])->name('denuncias.exportar');
    Route::get('/denuncias/{denuncia}', [DenunciaController::class, 'show'])->name('denuncias.show');

    Route::get('/segmentos', [SegmentosController::class, 'index'])->name('segmentos.index');
    Route::get('/pronosticos', [PronosticosController::class, 'index'])->name('pronosticos.index');
    Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');

    Route::resource('modalidades', ModalidadController::class)
        ->parameters(['modalidades' => 'modalidad'])
        ->except('show');
});
