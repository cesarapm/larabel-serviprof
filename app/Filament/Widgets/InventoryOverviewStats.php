<?php

namespace App\Filament\Widgets;

use App\Models\Consumable;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryOverviewStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalProducts = Product::query()->count();
        $availableProducts = Product::query()->where('inventory_status', 'disponible')->count();
        $rentedProducts = Product::query()->where('inventory_status', 'rentado')->count();
        $lowStockConsumables = Consumable::query()
            ->whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->count();

        return [
            Stat::make('Equipos totales', (string) $totalProducts)
                ->description('Inventario registrado'),
            Stat::make('Equipos disponibles', (string) $availableProducts)
                ->description('Listos para asignación'),
            Stat::make('Equipos rentados', (string) $rentedProducts)
                ->description('Actualmente en operación'),
            Stat::make('Consumibles bajo mínimo', (string) $lowStockConsumables)
                ->description('Requieren reposición'),
        ];
    }
}
