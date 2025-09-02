<?php

use App\Http\Controllers\CustomerController;
use App\Http\Middleware\AuthCustomer;
use App\Http\Middleware\VerifyIfTicketIsAvailable;
use Illuminate\Support\Facades\Route;

/**
 * Rotas públicas para cliente: 
 *   Cria | Autentíca
 */
Route::post('/customer', [CustomerController::class, 'store']);
Route::post('/customer/login', [CustomerController::class, 'authenticate']);

/**
 * Rotas privadas para gerenciar cliente: 
 *   Compra ingresso| Mostra um | Atualiza |Apaga (soft delete)
 */
Route::middleware(AuthCustomer::class)->group(function () {
    Route::post('/batch/{batch}/ticket', [CustomerController::class, 'buyTicket'])
        ->whereUuid('batch')
        ->middleware(VerifyIfTicketIsAvailable::class);
    
    Route::get('/customer',[CustomerController::class, 'show']);
    Route::put('/customer',[CustomerController::class, 'update']);
    Route::delete('/customer',[CustomerController::class, 'destroy']);
});