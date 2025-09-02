<?php

use App\Http\Controllers\AdminController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;

/**
 * Rotas públicas para admin: 
 *   Cria | Autentíca 
 */
Route::post('/admin', [AdminController::class, 'store']);
Route::post('/admin/login', [AdminController::class, 'authenticate']);


/**
 * Rotas privadas para gerenciar admin: 
 *   Mostra um | Atualiza |Apaga (soft delete)
 */
Route::middleware(AuthAdmin::class)->group(function () {

    Route::get('/admin',[AdminController::class, 'show']);
    Route::put('/admin',[AdminController::class, 'update']);
    Route::delete('/admin',[AdminController::class, 'destroy']);
});