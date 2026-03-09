<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (no session, no CSRF – for webhooks and external consumers)
|--------------------------------------------------------------------------
*/

Route::post('/webhook/payfast', [WebhookController::class, 'payfast'])->name('webhook.payfast');
