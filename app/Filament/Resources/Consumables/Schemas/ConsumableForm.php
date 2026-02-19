<?php

namespace App\Filament\Resources\Consumables\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConsumableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('minimum_stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('stock_reserved')
                    ->label('Stock reservado')
                    ->numeric()
                    ->default(0),
                TextInput::make('batch')
                    ->label('Lote')
                    ->maxLength(255),
                TextInput::make('supplier')
                    ->label('Proveedor')
                    ->maxLength(255),
            ]);
    }
}
