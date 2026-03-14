<?php

namespace App\Filament\Resources\EquipmentMovements\Schemas;

use App\Models\Location;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class EquipmentMovementForm
{
    /** Tipos de movimiento que sacan el equipo de la empresa (requieren ubicación destino). */
    private const OUTGOING_TYPES = ['salida', 'renta', 'venta', 'mantenimiento'];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Equipo (producto)')
                    ->relationship('product', 'id')
                    ->required()
                    ->live()
                    ->hint(function (Get $get): string {
                        $type = $get('type');
                        $productId = $get('product_id');
                        if (! $productId) {
                            return '';
                        }
                        $product = Product::with('location')->find($productId);
                        if (! $product) {
                            return '';
                        }
                        $location = $product->location?->name ?? 'Sin ubicación';
                        $status   = $product->inventory_status?->value ?? '';
                        if ($type === 'movimiento_interno') {
                            return "Ubicación actual: {$location} — Estado: {$status}";
                        }
                        return "Ubicación actual: {$location}";
                    })
                    ->hintColor(fn (Get $get): string =>
                        $get('type') === 'movimiento_interno' ? 'warning' : 'gray'
                    ),

                Select::make('type')
                    ->label('Tipo de movimiento')
                    ->options([
                        'entrada'            => 'Entrada',
                        'salida'             => 'Salida',
                        'renta'              => 'Renta',
                        'venta'              => 'Venta',
                        'mantenimiento'      => 'Mantenimiento',
                        'movimiento_interno' => 'Movimiento interno',
                    ])
                    ->required()
                    ->live(),

                Select::make('client_id')
                    ->label('Cliente destino')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set, Get $get) {
                        // Solo auto-rellena la ubicación si es un movimiento de salida
                        $type = $get('type');
                        if ($state && in_array($type, self::OUTGOING_TYPES, true)) {
                            $location = Location::where('client_id', $state)->first();
                            if ($location) {
                                $set('location_id', $location->id);
                            }
                        }
                    })
                    ->visible(fn (Get $get): bool => in_array($get('type'), self::OUTGOING_TYPES, true)),

                Select::make('location_id')
                    ->label(fn (Get $get): string => match (true) {
                        $get('type') === 'movimiento_interno'              => 'Nueva ubicación destino',
                        in_array($get('type'), self::OUTGOING_TYPES, true) => 'Ubicación destino',
                        default                                            => 'Ubicación',
                    })
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->required(fn (Get $get): bool => $get('type') === 'movimiento_interno')
                    ->helperText(fn (Get $get): string =>
                        $get('type') === 'movimiento_interno'
                            ? 'Selecciona la ubicación a donde se trasladará el equipo.'
                            : ''
                    ),

                Select::make('personnel_id')
                    ->label('Personal responsable')
                    ->relationship('personnel', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('current_counter_bw')
                    ->label('Contador B/N')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('current_counter_color')
                    ->label('Contador Color')
                    ->numeric()
                    ->minValue(0),
                DatePicker::make('counter_read_at')
                    ->label('Fecha lectura contadores'),
                DatePicker::make('date_out')
                    ->label('Fecha salida'),
                DatePicker::make('date_return')
                    ->label('Fecha retorno'),
                Textarea::make('notes')
                    ->label('Notas')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}

