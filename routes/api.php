<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // For production, wrap with middleware(['auth:sanctum','throttle:api'])
    Route::apiResource('customers', App\Http\Controllers\Api\V1\CustomerController::class);
    Route::apiResource('locations', App\Http\Controllers\Api\V1\LocationController::class);
    Route::apiResource('rates', App\Http\Controllers\Api\V1\RateController::class);

    Route::get('shipments', [App\Http\Controllers\Api\V1\ShipmentController::class, 'index']);
    Route::post('shipments', [App\Http\Controllers\Api\V1\ShipmentController::class, 'store']);
    Route::get('shipments/{shipment}', [App\Http\Controllers\Api\V1\ShipmentController::class, 'show']);
    Route::put('shipments/{shipment}', [App\Http\Controllers\Api\V1\ShipmentController::class, 'update']);
    Route::post('shipments/{shipment}/transition', [App\Http\Controllers\Api\V1\ShipmentController::class, 'transition']);

    Route::get('invoices', [App\Http\Controllers\Api\V1\InvoiceController::class, 'index']);
    Route::post('invoices', [App\Http\Controllers\Api\V1\InvoiceController::class, 'store']);
    Route::get('invoices/{invoice}', [App\Http\Controllers\Api\V1\InvoiceController::class, 'show']);
    Route::put('invoices/{invoice}', [App\Http\Controllers\Api\V1\InvoiceController::class, 'update']);
    Route::post('invoices/generate', [App\Http\Controllers\Api\V1\InvoiceController::class, 'generate']);
    Route::post('invoices/{invoice}/send', [App\Http\Controllers\Api\V1\InvoiceController::class, 'send']);

    Route::post('payments', [App\Http\Controllers\Api\V1\PaymentController::class, 'store']);

    Route::post('webhooks/shipment-status', function () {
        // TODO: verify signature header and process
        return response()->json(['ok' => true]);
    });
});

