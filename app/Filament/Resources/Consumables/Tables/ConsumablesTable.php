<?php

namespace App\Filament\Resources\Consumables\Tables;

use App\Filament\Exports\ConsumableExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ConsumablesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('part_number')
                    ->label('N/P')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->label('Serie')
                    ->searchable(),
                TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),
                TextColumn::make('model')
                    ->label('Modelo')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge(),
                TextColumn::make('unit')
                    ->label('Unidad')
                    ->badge(),
                TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('minimum_stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_reserved')
                    ->label('Reservado')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('batch')
                    ->label('Lote')
                    ->searchable(),
                TextColumn::make('supplier')
                    ->label('Proveedor')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->label('Ubicación')
                    ->searchable(),
                TextColumn::make('sub_location')
                    ->label('Sub ubicación')
                    ->searchable(),
                TextColumn::make('notes')
                    ->label('Nota')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Filter::make('low_stock')
                    ->label('Stock bajo')
                    ->query(fn ($query) => $query->whereRaw('(stock_quantity - stock_reserved) <= minimum_stock')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(ConsumableExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(ConsumableExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
