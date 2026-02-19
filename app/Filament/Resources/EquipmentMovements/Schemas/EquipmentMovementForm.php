<?php

namespace App\Filament\Resources\EquipmentMovements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                Select::make('type')
                    ->options([
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            'renta' => 'Renta',
            'venta' => 'Venta',
            'mantenimiento' => 'Mantenimiento',
        ])
                    ->required(),
                DatePicker::make('date_out'),
                DatePicker::make('date_return'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
