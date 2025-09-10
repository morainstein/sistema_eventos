<?php

use App\Http\Controllers\DiscountController;
use App\Http\Controllers\PaggueCredentialsController;
use App\Http\Controllers\PromoterController;
use App\Http\Middleware\AuthPromoter;
use Illuminate\Support\Facades\Route;

/**
 * Rotas públicas para promotor: 
 *   Cria | Autentíca | Mostra um promotor
 */
Route::post('/promoter', [PromoterController::class, 'store']);
Route::post('/promoter/login', [PromoterController::class, 'authenticate']);
Route::get('/promoter/{promoter}', [PromoterController::class, 'show'])
    ->whereUuid('promoter');

/**
 * Rotas privadas para gerenciar promotor: 
 *   Atualiza | Apaga (soft delete)
 */
Route::middleware(AuthPromoter::class)->group(function () {
    Route::put('/promoter', [PromoterController::class, 'update']);
    Route::delete('/promoter', [PromoterController::class, 'destroy']);
});

/**
 * Rotas privadas para gerenciar credenciais da Paggue: 
 *   Cria e/ou substitui | Apaga 
 */
Route::middleware(AuthPromoter::class)->group(function () {
    Route::post('/promoter/credentials/paggue',[PaggueCredentialsController::class,'store']);
    Route::delete('/promoter/credentials/paggue',[PaggueCredentialsController::class,'destroy']);
});

/**
 * Rotas privadas para gerenciar cupons de desconto de um evento:
 *   Retorna todos | Cria | Apaga
 */
Route::middleware(AuthPromoter::class)->group(function () {
    Route::get('/promoter/{event}/discount',[DiscountController::class,'index'])
        ->whereUuid('event');
    Route::post('/promoter/discount',[DiscountController::class,'store']);
    Route::delete('/promoter/discount/{discount}',[DiscountController::class,'destroy']);
});