<?php

use App\Http\Controllers\EventController;
use App\Http\Middleware\AuthPromoter;
use Illuminate\Support\Facades\Route;

Route::post('/events', [EventController::class, 'store'])->middleware(AuthPromoter::class);

Route::get('/events', [EventController::class, 'index']);

Route::get('/events/{eventId}', [EventController::class, 'show'])->whereUuid('eventId');
