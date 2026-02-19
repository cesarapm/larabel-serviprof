<?php

namespace App\Filament\Exports;

use App\Models\Consumable;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ConsumableExporter extends Exporter
{
    protected static ?string $model = Consumable::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('stock_quantity'),
            ExportColumn::make('minimum_stock'),
            ExportColumn::make('stock_reserved'),
            ExportColumn::make('batch'),
            ExportColumn::make('supplier'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de consumibles finalizó con ' . Number::format($export->successful_rows) . ' ' . str('registro')->plural($export->successful_rows) . ' exportados.';

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
