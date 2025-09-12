<?php

use App\Http\Controllers\EventController;
use App\Http\Middleware\AuthPromoter;
use App\Http\Middleware\VerifyIfPaymentCredentialsExists;
use Illuminate\Support\Facades\Route;

/**
 * Rotas públicas para eventos:
 *   Retorna todos | Retorna um
 */
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{eventId}', [EventController::class, 'show'])
    ->whereUuid('eventId');

/**
 * Rotas privadas para gerenciar eventos:
 *   Cria | Recebe upload da imagem do banner do evento | Atualiza | Apaga
 */
Route::middleware(AuthPromoter::class)->group(function (){
    Route::post('/events', [EventController::class, 'store'])
        ->middleware(VerifyIfPaymentCredentialsExists::class);
        
    Route::post('/events/{event}/banner', [EventController::class, 'uploadedBanner'])
        ->whereUuid('event');

    Route::put('/events/{event}',[EventController::class,'update'])
        ->middleware(VerifyIfPaymentCredentialsExists::class)
        ->whereUuid('event');
        
    Route::delete('/events/{event}',[EventController::class,'destroy'])
        ->whereUuid('event');
});

