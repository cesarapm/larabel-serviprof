<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;



class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(),
          Select::make('status')
    ->options([
        'admin' => 'Administrador',
        'usuario' => 'Usuario',

    ])

            ]);
    }
}
