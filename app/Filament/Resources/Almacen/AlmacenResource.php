<?php

namespace App\Filament\Resources\Almacen;

use App\Filament\Resources\Almacen\Pages\ListAlmacen;
use App\Filament\Resources\Almacen\Tables\AlmacenTable;
use App\Models\Almacen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AlmacenResource extends Resource
{
    protected static ?string $model = Almacen::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Almacén';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'entrada de almacén';

    protected static ?string $pluralModelLabel = 'Almacén — Inventario';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return AlmacenTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['product', 'consumable', 'location']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlmacen::route('/'),
        ];
    }

    /** Solo lectura: sin botón Crear ni acciones de edición/borrado */
    public static function canCreate(): bool
    {
        return false;
    }
}
