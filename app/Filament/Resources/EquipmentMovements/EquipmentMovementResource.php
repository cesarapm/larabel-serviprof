<?php

namespace App\Filament\Resources\EquipmentMovements;

use App\Filament\Resources\EquipmentMovements\Pages\CreateEquipmentMovement;
use App\Filament\Resources\EquipmentMovements\Pages\EditEquipmentMovement;
use App\Filament\Resources\EquipmentMovements\Pages\ListEquipmentMovements;
use App\Filament\Resources\EquipmentMovements\Schemas\EquipmentMovementForm;
use App\Filament\Resources\EquipmentMovements\Tables\EquipmentMovementsTable;
use App\Models\EquipmentMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EquipmentMovementResource extends Resource
{
    protected static ?string $model = EquipmentMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Movimientos';

    protected static string|\UnitEnum|null $navigationGroup = 'Procesos';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EquipmentMovementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquipmentMovementsTable::configure($table);
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
            'index' => ListEquipmentMovements::route('/'),
            'create' => CreateEquipmentMovement::route('/create'),
            'edit' => EditEquipmentMovement::route('/{record}/edit'),
        ];
    }
}
