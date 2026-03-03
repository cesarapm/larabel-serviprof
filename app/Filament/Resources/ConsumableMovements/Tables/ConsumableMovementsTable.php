<?php

namespace App\Filament\Resources\ConsumableMovements\Tables;

use App\Filament\Exports\ConsumableMovementExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ConsumableMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('consumable.name')
                    ->label('Consumible')
                    ->searchable(),
                TextColumn::make('consumable.status')
                    ->label('Estatus')
                    ->badge(),
                TextColumn::make('consumable.location.name')
                    ->label('Ubicación')
                    ->searchable(),
                TextColumn::make('consumable.sub_location')
                    ->label('Sub ubicación')
                    ->searchable(),
                TextColumn::make('consumable.unit')
                    ->label('Unidad')
                    ->badge(),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->label('Ubicación mov.')
                    ->searchable(),
                TextColumn::make('personnel.name')
                    ->label('Personal')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('movement_date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(40),
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
                        'ajuste' => 'Ajuste',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(ConsumableMovementExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(ConsumableMovementExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
