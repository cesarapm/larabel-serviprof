<?php

namespace App\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;
use App\Models\Personnel;

class PersonnelExporter extends Exporter
{
    protected static ?string $model = Personnel::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('position'),
            ExportColumn::make('phone'),
            ExportColumn::make('email'),
            ExportColumn::make('is_active'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de personal finalizó con ' . Number::format($export->successful_rows) . ' ' . str('registro')->plural($export->successful_rows) . ' exportados.';

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
