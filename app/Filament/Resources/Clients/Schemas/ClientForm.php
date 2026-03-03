<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('address')
                    ->default(null),
                TextInput::make('rfc')
                    ->label('RFC')
                    ->maxLength(13)
                    ->default(null),
                TextInput::make('company')
                    ->default(null),
                TextInput::make('contact_name')
                    ->label('Contact Name')
                    ->default(null),
                TextInput::make('department')
                    ->default(null),
            ]);
    }
}
