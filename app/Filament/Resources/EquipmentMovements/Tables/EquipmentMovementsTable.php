<?php

namespace App\Filament\Resources\EquipmentMovements\Tables;

use App\Filament\Exports\EquipmentMovementExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EquipmentMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.id')
                    ->searchable(),
                TextColumn::make('client.name')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('date_out')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_return')
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
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'renta' => 'Renta',
                        'venta' => 'Venta',
                        'mantenimiento' => 'Mantenimiento',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(EquipmentMovementExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(EquipmentMovementExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
