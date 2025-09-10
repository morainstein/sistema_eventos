<?php

use App\Http\Controllers\WebhookController;
use App\Http\Middleware\ValidateSignatureCashInPaggue;
use Illuminate\Support\Facades\Route;

/**
 * Webhook para receber status de pagamento de ingresso comprado
 */
Route::post('/webhook/paggue/pix/cash-in', [WebhookController::class, 'pixCashIn'])
    ->name('paggue.webhook.cash-in')
    ->middleware(ValidateSignatureCashInPaggue::class);
