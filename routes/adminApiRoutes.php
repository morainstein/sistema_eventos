<?php

use App\Http\Controllers\AdminController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;

Route::post('/admin/register', [AdminController::class, 'store']);
Route::post('/admin/login', [AdminController::class, 'authenticate']);

Route::middleware(AuthAdmin::class)->group(function () {

});