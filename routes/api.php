<?php

use App\Http\Controllers\EventController;
use App\Http\Middleware\AuthPromoter;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    dd('API is working!');
});

/**
 * Events Routens
 */
include_once 'eventApiRoutes.php';

/**
 * Admin Routes
 */
include_once 'adminApiRoutes.php';

/**
 * Promoter Routes
 */
include_once 'promoterApiRoutes.php';

/**
 * Customer Routes
*/
include_once 'customerApiRoutes.php';
