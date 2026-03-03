<?php

namespace App\Filament\Resources\Personnel\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PersonnelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('position')
                    ->label('Puesto')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->maxLength(30),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }
}
