<?php

namespace App\Filament\Resources\Consumables;

use App\Filament\Resources\Consumables\Pages\CreateConsumable;
use App\Filament\Resources\Consumables\Pages\EditConsumable;
use App\Filament\Resources\Consumables\Pages\ListConsumables;
use App\Filament\Resources\Consumables\Schemas\ConsumableForm;
use App\Filament\Resources\Consumables\Tables\ConsumablesTable;
use App\Models\Consumable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConsumableResource extends Resource
{
    protected static ?string $model = Consumable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Consumibles';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ConsumableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsumablesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsumables::route('/'),
            'create' => CreateConsumable::route('/create'),
            'edit' => EditConsumable::route('/{record}/edit'),
        ];
    }
}
