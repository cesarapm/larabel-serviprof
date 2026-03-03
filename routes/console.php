<?php

use App\Models\Consumable;
use App\Models\ConsumableMovement;
use App\Models\EquipmentMovement;
use App\Models\Product;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('inventory:recalculate', function (): int {
    $productsProcessed = 0;
    $consumablesProcessed = 0;

    Product::query()
        ->select('id')
        ->orderBy('id')
        ->chunkById(200, function ($products) use (&$productsProcessed): void {
            foreach ($products as $product) {
                EquipmentMovement::recalculateProductInventoryStatus((int) $product->id);
                $productsProcessed++;
            }
        });

    Consumable::query()
        ->select('id')
        ->orderBy('id')
        ->chunkById(200, function ($consumables) use (&$consumablesProcessed): void {
            foreach ($consumables as $consumable) {
                ConsumableMovement::recalculateConsumableInventory((int) $consumable->id);
                $consumablesProcessed++;
            }
        });

    $this->info("Recalculación completa. Equipos: {$productsProcessed}. Consumibles: {$consumablesProcessed}.");

    return self::SUCCESS;
})->purpose('Recalcula inventory_status de equipos y consumibles en lote');
