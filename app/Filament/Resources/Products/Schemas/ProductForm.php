<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\EquipmentStatus;
use App\Enums\InventoryStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(['copiadora' => 'Copiadora', 'impresora' => 'Impresora'])
                    ->required(),
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('serial_number')
                    ->required(),
                TextInput::make('spd_internal_id')
                    ->label('ID interno SPD / QR')
                    ->required()
                    ->maxLength(255),
                TextInput::make('current_counter_bw')
                    ->label('Contador actual BN')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('current_counter_color')
                    ->label('Contador actual Color')
                    ->numeric()
                    ->minValue(0),
                DatePicker::make('counter_read_at')
                    ->label('Fecha de contador'),
                Select::make('status')
                    ->options(EquipmentStatus::class)
                    ->required(),
                Select::make('inventory_status')
                    ->options(InventoryStatus::class)
                    ->default('disponible')
                    ->required(),
                Select::make('classification')
                    ->label('Clasificación')
                    ->options([
                        'renta' => 'Renta',
                        'venta' => 'Venta',
                        'refaccion' => 'Refacción',
                        'demo' => 'Demo',
                        'taller' => 'Taller',
                    ])
                    ->required(),
                Select::make('commercial_condition')
                    ->label('Condición comercial')
                    ->options([
                        'a1' => 'A1',
                        'a2' => 'A2',
                        'b' => 'B',
                        'c' => 'C',
                    ])
                    ->required(),
                TextInput::make('acquisition_cost')
                    ->label('Costo de adquisición')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                TextInput::make('supplier')
                    ->label('Proveedor')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('acquisition_date')
                    ->label('Fecha de adquisición')
                    ->required(),
                TextInput::make('book_value')
                    ->label('Valor contable')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('depreciation_amount')
                    ->label('Depreciación acumulada')
                    ->numeric()
                    ->prefix('$'),
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required(),
                DatePicker::make('entry_date')
                    ->required(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
