<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Exports\ProductExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->searchable(),
                TextColumn::make('spd_internal_id')
                    ->label('SPD/QR')
                    ->searchable(),
                TextColumn::make('current_counter_bw')
                    ->label('Contador BN')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_counter_color')
                    ->label('Contador Color')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('counter_read_at')
                    ->label('Fecha contador')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('inventory_status')
                    ->badge(),
                TextColumn::make('classification')
                    ->label('Clasificación')
                    ->badge(),
                TextColumn::make('commercial_condition')
                    ->label('Condición')
                    ->badge(),
                TextColumn::make('acquisition_cost')
                    ->label('Costo')
                    ->money('MXN')
                    ->sortable(),
                TextColumn::make('supplier')
                    ->label('Proveedor')
                    ->searchable(),
                TextColumn::make('acquisition_date')
                    ->label('Fecha adquisición')
                    ->date()
                    ->sortable(),
                TextColumn::make('book_value')
                    ->label('Valor contable')
                    ->money('MXN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('depreciation_amount')
                    ->label('Depreciación')
                    ->money('MXN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('location.name')
                    ->searchable(),
                TextColumn::make('entry_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'copiadora' => 'Copiadora',
                        'impresora' => 'Impresora',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'nuevo' => 'Nuevo',
                        'usado' => 'Usado',
                        'renta' => 'Renta',
                        'reparacion' => 'Reparación',
                    ]),
                SelectFilter::make('inventory_status')
                    ->options([
                        'disponible' => 'Disponible',
                        'rentado' => 'Rentado',
                        'vendido' => 'Vendido',
                        'mantenimiento' => 'Mantenimiento',
                    ]),
                SelectFilter::make('classification')
                    ->options([
                        'renta' => 'Renta',
                        'venta' => 'Venta',
                        'refaccion' => 'Refacción',
                        'demo' => 'Demo',
                        'taller' => 'Taller',
                    ]),
                SelectFilter::make('commercial_condition')
                    ->options([
                        'a1' => 'A1',
                        'a2' => 'A2',
                        'b' => 'B',
                        'c' => 'C',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(ProductExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(ProductExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
