<?php

use App\Http\Controllers\CustomerController;
use App\Http\Middleware\AuthCustomer;
use Illuminate\Support\Facades\Route;

Route::post('/customer/register', [CustomerController::class, 'store']);
Route::post('/customer/login', [CustomerController::class, 'authenticate']);

Route::middleware(AuthCustomer::class)->group(function () {

});