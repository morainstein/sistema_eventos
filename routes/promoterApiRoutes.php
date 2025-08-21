<?php

use App\Http\Controllers\PromoterController;
use App\Http\Middleware\AuthPromoter;
use Illuminate\Support\Facades\Route;

Route::post('/promoter/register', [PromoterController::class, 'store']);
Route::post('/promoter/login', [PromoterController::class, 'authenticate']);

Route::middleware(AuthPromoter::class)->group(function () {
    
});
