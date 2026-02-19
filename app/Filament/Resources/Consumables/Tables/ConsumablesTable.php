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
                TextColumn::make('name')
                    ->searchable(),
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
