<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ConsumableController;
use App\Http\Controllers\Api\EquipmentMovementController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('products', ProductController::class);
    Route::apiResource('locations', LocationController::class);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('equipment-movements', EquipmentMovementController::class);
    Route::apiResource('consumables', ConsumableController::class);
});
