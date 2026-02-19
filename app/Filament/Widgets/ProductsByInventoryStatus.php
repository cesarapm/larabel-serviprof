<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProductsByInventoryStatus extends ChartWidget
{
    protected ?string $heading = 'Equipos por estado de inventario';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $statuses = [
            'disponible' => 'Disponible',
            'rentado' => 'Rentado',
            'vendido' => 'Vendido',
            'mantenimiento' => 'Mantenimiento',
        ];

        $counts = Product::query()
            ->selectRaw('inventory_status, COUNT(*) as total')
            ->groupBy('inventory_status')
            ->pluck('total', 'inventory_status');

        return [
            'datasets' => [[
                'label' => 'Equipos',
                'data' => array_map(
                    fn (string $status): int => (int) ($counts[$status] ?? 0),
                    array_keys($statuses),
                ),
            ]],
            'labels' => array_values($statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
