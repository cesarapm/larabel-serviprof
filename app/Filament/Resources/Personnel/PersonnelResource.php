<?php

namespace App\Filament\Resources\Personnel;

use App\Filament\Resources\Personnel\Pages\CreatePersonnel;
use App\Filament\Resources\Personnel\Pages\EditPersonnel;
use App\Filament\Resources\Personnel\Pages\ListPersonnel;
use App\Filament\Resources\Personnel\Schemas\PersonnelForm;
use App\Filament\Resources\Personnel\Tables\PersonnelTable;
use App\Models\Personnel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PersonnelResource extends Resource
{
    protected static ?string $model = Personnel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Personal';

    protected static string|\UnitEnum|null $navigationGroup = 'Catálogos';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PersonnelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PersonnelTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPersonnel::route('/'),
            'create' => CreatePersonnel::route('/create'),
            'edit' => EditPersonnel::route('/{record}/edit'),
        ];
    }
}
