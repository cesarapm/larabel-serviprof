<?php

namespace App\Filament\Resources\ConsumableMovements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConsumableMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('consumable_id')
                    ->label('Consumible')
                    ->relationship('consumable', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('client_id')
                    ->label('Cliente destino')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
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
                    ->label('Tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'ajuste' => 'Ajuste',
                    ])
                    ->required(),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                DatePicker::make('movement_date')
                    ->label('Fecha de movimiento')
                    ->required(),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }
}
