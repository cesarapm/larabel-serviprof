<?php

namespace App\Filament\Resources\ConsumableMovements;

use App\Filament\Resources\ConsumableMovements\Pages\CreateConsumableMovement;
use App\Filament\Resources\ConsumableMovements\Pages\EditConsumableMovement;
use App\Filament\Resources\ConsumableMovements\Pages\ListConsumableMovements;
use App\Filament\Resources\ConsumableMovements\Schemas\ConsumableMovementForm;
use App\Filament\Resources\ConsumableMovements\Tables\ConsumableMovementsTable;
use App\Models\ConsumableMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConsumableMovementResource extends Resource
{
    protected static ?string $model = ConsumableMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPathRoundedSquare;

    protected static ?string $navigationLabel = 'Mov. consumibles';

    protected static string|\UnitEnum|null $navigationGroup = 'Movimientos';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ConsumableMovementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsumableMovementsTable::configure($table);
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
            'index' => ListConsumableMovements::route('/'),
            'create' => CreateConsumableMovement::route('/create'),
            'edit' => EditConsumableMovement::route('/{record}/edit'),
        ];
    }
}
