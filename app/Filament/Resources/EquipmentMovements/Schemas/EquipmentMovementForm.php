<?php

namespace App\Filament\Resources\EquipmentMovements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EquipmentMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'id')
                    ->required(),
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->default(null),
                Select::make('location_id')
                    ->label('Ubicación movimiento')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('personnel_id')
                    ->label('Personal responsable')
                    ->relationship('personnel', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->options([
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            'renta' => 'Renta',
            'venta' => 'Venta',
            'mantenimiento' => 'Mantenimiento',
        ])
                    ->required(),
                TextInput::make('current_counter_bw')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('current_counter_color')
                    ->numeric()
                    ->minValue(0),
                DatePicker::make('counter_read_at'),
                DatePicker::make('date_out'),
                DatePicker::make('date_return'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
