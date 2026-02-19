<?php

namespace App\Filament\Exports;

use App\Models\EquipmentMovement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EquipmentMovementExporter extends Exporter
{
    protected static ?string $model = EquipmentMovement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('product.id'),
            ExportColumn::make('client.name'),
            ExportColumn::make('type'),
            ExportColumn::make('date_out'),
            ExportColumn::make('date_return'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de movimientos finalizó con ' . Number::format($export->successful_rows) . ' ' . str('registro')->plural($export->successful_rows) . ' exportados.';

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
