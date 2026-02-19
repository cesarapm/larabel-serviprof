<?php

namespace App\Filament\Widgets;

use App\Models\EquipmentMovement;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MonthlyMovementsChart extends ChartWidget
{
    protected ?string $heading = 'Movimientos por mes (últimos 6 meses)';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect(range(5, 0, -1))
            ->map(fn (int $offset): Carbon => now()->startOfMonth()->subMonths($offset))
            ->push(now()->startOfMonth());

        $labels = $months
            ->map(fn (Carbon $date): string => $date->translatedFormat('M Y'))
            ->values();

        $dataset = $months
            ->map(function (Carbon $date): int {
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();

                return EquipmentMovement::query()
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
            })
            ->values();

        return [
            'datasets' => [[
                'label' => 'Movimientos',
                'data' => $dataset,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
