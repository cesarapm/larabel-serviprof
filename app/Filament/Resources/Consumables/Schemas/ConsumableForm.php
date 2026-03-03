<?php

namespace App\Filament\Resources\Consumables\Schemas;

use App\Enums\EquipmentStatus;
use App\Models\Personnel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ConsumableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'refaccion' => 'Refacción',
                        'tinta' => 'Tinta',
                        'toner' => 'Tóner',
                        'otras' => 'Otras',
                    ])
                    ->required(),
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('part_number')
                    ->label('N/P')
                    ->maxLength(255),
                TextInput::make('serial_number')
                    ->label('Serie')
                    ->maxLength(255),
                TextInput::make('brand')
                    ->label('Marca')
                    ->maxLength(255),
                TextInput::make('model')
                    ->label('Modelo')
                    ->maxLength(255),
                Select::make('status')
                    ->label('Estatus')
                    ->options(EquipmentStatus::class)
                    ->required(),
                Select::make('inventory_status')
                    ->label('Estatus inventario')
                    ->options([
                   'disponible' => 'Disponible',
                   'rentado' => 'Rentado',
                    'vendido' => 'Vendido',
                    'mantenimiento' => 'Mantenimiento'
                    ])
                    ->default('disponible')
                    ->required()
                    ->visibleOn('create'),
                Select::make('unit')
                    ->label('Unidad')
                    ->options([
                        'pieza' => 'Pieza',
                        'caja' => 'Caja',
                        'kit' => 'Kit',
                        'litro' => 'Litro',
                        'ml' => 'ML',
                    ])
                    ->default('pieza')
                    ->required(),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('minimum_stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('stock_reserved')
                    ->label('Stock reservado')
                    ->numeric()
                    ->default(0),
                TextInput::make('batch')
                    ->label('Lote')
                    ->maxLength(255),
                TextInput::make('supplier')
                    ->label('Proveedor')
                    ->maxLength(255),
                Select::make('location_id')
                    ->label('Ubicación')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visibleOn('create'),
                Select::make('personnel_id')
                    ->label('Personal alta')
                    ->options(fn () => Personnel::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visibleOn('create'),
                TextInput::make('sub_location')
                    ->label('Sub ubicación')
                    ->maxLength(255),
                Textarea::make('notes')
                    ->label('Nota')
                    ->columnSpanFull(),
            ]);
    }
}
