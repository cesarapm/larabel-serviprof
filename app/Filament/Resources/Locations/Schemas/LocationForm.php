<?php

namespace App\Filament\Resources\Locations\Schemas;

use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('sub_location')
                    ->label('Sub-ubicación (rack / zona)')
                    ->maxLength(255),
                Select::make('type')
                    ->options([
                        'almacen_apodaca' => 'Almacén Apodaca',
                        'taller' => 'Taller',
                        'transito' => 'Tránsito',
                        'cliente' => 'Cliente',
                        'baja_canibalizacion' => 'Baja / Canibalización',
                        'demo_showroom' => 'Demo / Showroom',
                    ])
                    ->required(),
                Select::make('client_id')
                    ->label('Cliente (solo si tipo = Cliente)')
                    ->options(fn () => Client::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->nullable(),
            ]);
    }
}
