<?php

namespace App\Filament\Resources\ConsumableMovements\Schemas;

use App\Models\Consumable;
use App\Models\Location;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ConsumableMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('consumable_id')
                    ->label('Consumible')
                    ->relationship('consumable', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->hint(function (Get $get): string {
                        $consumableId = $get('consumable_id');
                        if (! $consumableId) {
                            return '';
                        }
                        $consumable = Consumable::with('location')->find($consumableId);
                        if (! $consumable) {
                            return '';
                        }
                        $location = $consumable->location?->name ?? 'Sin ubicación';
                        return "Stock: {$consumable->stock_quantity} — Ubicación actual: {$location}";
                    })
                    ->hintColor('info'),

                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'entrada'            => 'Entrada',
                        'salida'             => 'Salida',
                        'ajuste'             => 'Ajuste',
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
                        // Solo auto-rellena la ubicación en salidas
                        if ($state && $get('type') === 'salida') {
                            $location = Location::where('client_id', $state)->first();
                            if ($location) {
                                $set('location_id', $location->id);
                            }
                        }
                    })
                    ->visible(fn (Get $get): bool => $get('type') === 'salida'),

                Select::make('location_id')
                    ->label(fn (Get $get): string => match ($get('type')) {
                        'salida'             => 'Ubicación final destino',
                        'movimiento_interno' => 'Ubicación destino',
                        default              => 'Ubicación movimiento',
                    })
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('personnel_id')
                    ->label('Personal responsable')
                    ->relationship('personnel', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->hint(function (Get $get): string {
                        $type = $get('type');
                        if (! in_array($type, ['salida', 'movimiento_interno'])) {
                            return '';
                        }
                        $consumableId = $get('consumable_id');
                        $consumable = $consumableId ? Consumable::find($consumableId) : null;
                        if (! $consumable) {
                            return '';
                        }
                        return "Máximo disponible: {$consumable->stock_quantity}";
                    })
                    ->hintColor('warning')
                    ->maxValue(function (Get $get): ?int {
                        $type = $get('type');
                        if (! in_array($type, ['salida', 'movimiento_interno'])) {
                            return null;
                        }
                        $consumableId = $get('consumable_id');
                        $consumable = $consumableId ? Consumable::find($consumableId) : null;
                        return $consumable?->stock_quantity;
                    }),

                DatePicker::make('movement_date')
                    ->label('Fecha de movimiento')
                    ->required(),

                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }
}

