<?php

use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BulkMovementController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ConsumableController;
use App\Http\Controllers\Api\ConsumableMovementController;
use App\Http\Controllers\Api\EquipmentMovementController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PersonnelController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('products', ProductController::class);
    Route::apiResource('locations', LocationController::class);
    Route::apiResource('clients', ClientController::class);

    // Almacén: inventario por ubicación
    Route::get('almacen', [AlmacenController::class, 'index']);
    Route::get('almacen/{almacen}', [AlmacenController::class, 'show']);

    // Movimientos en lote (deben ir ANTES de apiResource para evitar conflictos de ruta)
    Route::post('bulk-movements', [BulkMovementController::class, 'mixed']);
    Route::post('equipment-movements/bulk', [BulkMovementController::class, 'equipment']);
    Route::post('consumable-movements/bulk', [BulkMovementController::class, 'consumables']);

    Route::apiResource('equipment-movements', EquipmentMovementController::class);
    Route::post('equipment-movements/{equipmentMovement}/retorno', [EquipmentMovementController::class, 'retorno']);
    Route::apiResource('consumables', ConsumableController::class);
    Route::apiResource('consumable-movements', ConsumableMovementController::class);
    Route::get('personnel', [PersonnelController::class, 'index']);
    Route::get('personnel/{personnel}', [PersonnelController::class, 'show']);
});
