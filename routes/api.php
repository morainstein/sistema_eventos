<?php

use Illuminate\Support\Facades\Route;

Route::get('/teste', function () {

    dd('API is working!');
});

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
