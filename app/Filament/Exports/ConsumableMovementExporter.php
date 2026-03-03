<?php

namespace App\Filament\Exports;

use App\Models\ConsumableMovement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ConsumableMovementExporter extends Exporter
{
    protected static ?string $model = ConsumableMovement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('consumable.name'),
            ExportColumn::make('client.name'),
            ExportColumn::make('location.name'),
            ExportColumn::make('personnel.name'),
            ExportColumn::make('type'),
            ExportColumn::make('quantity'),
            ExportColumn::make('movement_date'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de movimientos de consumibles finalizó con ' . Number::format($export->successful_rows) . ' ' . str('registro')->plural($export->successful_rows) . ' exportados.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('registro')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
