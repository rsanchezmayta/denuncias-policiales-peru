<?php

use App\Http\Controllers\Api\DenunciaApiController;
use App\Http\Controllers\Api\SegmentoApiController;
use Illuminate\Support\Facades\Route;

Route::get('/denuncias', [DenunciaApiController::class, 'index']);
Route::get('/denuncias/resumen', [DenunciaApiController::class, 'resumen']);
Route::get('/segmentos', [SegmentoApiController::class, 'index']);
