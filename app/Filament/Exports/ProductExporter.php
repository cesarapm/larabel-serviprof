<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('type'),
            ExportColumn::make('brand'),
            ExportColumn::make('model'),
            ExportColumn::make('serial_number'),
            ExportColumn::make('spd_internal_id'),
            ExportColumn::make('current_counter_bw'),
            ExportColumn::make('current_counter_color'),
            ExportColumn::make('counter_read_at'),
            ExportColumn::make('status'),
            ExportColumn::make('inventory_status'),
            ExportColumn::make('classification'),
            ExportColumn::make('commercial_condition'),
            ExportColumn::make('acquisition_cost'),
            ExportColumn::make('supplier'),
            ExportColumn::make('acquisition_date'),
            ExportColumn::make('book_value'),
            ExportColumn::make('depreciation_amount'),
            ExportColumn::make('location.name'),
            ExportColumn::make('entry_date'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de equipos finalizó con ' . Number::format($export->successful_rows) . ' ' . str('registro')->plural($export->successful_rows) . ' exportados.';

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
