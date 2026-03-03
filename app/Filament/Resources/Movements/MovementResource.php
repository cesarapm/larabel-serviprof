<?php

namespace App\Filament\Resources\Movements;

use App\Filament\Resources\Movements\Pages\ListMovements;
use App\Filament\Resources\Movements\Tables\MovementsTable;
use App\Models\Movement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MovementResource extends Resource
{
    protected static ?string $model = Movement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Todos';

    protected static string|\UnitEnum|null $navigationGroup = 'Movimientos';

    protected static ?int $navigationSort = 0;

    public static function table(Table $table): Table
    {
        return MovementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMovements::route('/'),
        ];
    }
}
