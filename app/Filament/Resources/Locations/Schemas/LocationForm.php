<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocationForm
{
    /**
     * Tipos de ubicación que se pueden crear manualmente.
     * El tipo 'cliente' se genera automáticamente al crear un Cliente.
     */
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
                    ->label('Tipo')
                    ->options([
                        'almacen_apodaca'     => 'Almacén Apodaca',
                        'taller'              => 'Taller',
                        'transito'            => 'Tránsito',
                        'baja_canibalizacion' => 'Baja / Canibalización',
                        'demo_showroom'       => 'Demo / Showroom',
                    ])
                    ->helperText('Las ubicaciones de cliente se crean automáticamente al registrar un cliente.')
                    ->required(),
                // client_id se gestiona solo de forma automática; no se expone en el formulario manual.
            ]);
    }
}
