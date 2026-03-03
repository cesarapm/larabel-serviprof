<?php

namespace App\Filament\Resources\Movements\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('movement_date', 'desc')
            ->columns([
                TextColumn::make('source')
                    ->label('Origen')
                    ->badge(),
                TextColumn::make('item_name')
                    ->label('Elemento')
                    ->searchable(),
                TextColumn::make('movement_type')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('location_name')
                    ->label('Ubicación')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sub_location')
                    ->label('Sub ubicación')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('unit')
                    ->label('Unidad')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_counter_bw')
                    ->label('Contador BN')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('current_counter_color')
                    ->label('Contador color')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('counter_read_at')
                    ->label('Fecha contador')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('personnel_name')
                    ->label('Personal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('movement_date')
                    ->label('Fecha movimiento')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_return')
                    ->label('Fecha retorno')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(40),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label('Origen')
                    ->options([
                        'equipo' => 'Equipo',
                        'consumible' => 'Consumible',
                    ]),
                SelectFilter::make('movement_type')
                    ->label('Tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'renta' => 'Renta',
                        'venta' => 'Venta',
                        'mantenimiento' => 'Mantenimiento',
                        'ajuste' => 'Ajuste',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
