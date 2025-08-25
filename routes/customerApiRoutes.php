<?php

use App\Http\Controllers\CustomerController;
use App\Http\Middleware\AuthCustomer;
use App\Http\Middleware\VerifyIfTicketIsAvailable;
use Illuminate\Support\Facades\Route;

Route::post('/customer', [CustomerController::class, 'store']);
Route::post('/customer/login', [CustomerController::class, 'authenticate']);

Route::middleware(AuthCustomer::class)->group(function () {
    Route::post('/batch/{batch}/ticket', [CustomerController::class, 'buyTicket'])
        // ->whereUuid('batch')
        ->middleware(VerifyIfTicketIsAvailable::class);
});