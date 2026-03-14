<?php

namespace App\Filament\Resources\Almacen\Tables;

use App\Models\Location;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AlmacenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('location.name')
                    ->label('Ubicación')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->state(fn ($record): string => $record->product_id ? 'Equipo' : 'Consumible')
                    ->badge()
                    ->color(fn ($state): string => $state === 'Equipo' ? 'warning' : 'success'),

                TextColumn::make('id_interno')
                    ->label('ID Interno / N/P')
                    ->state(function ($record): string {
                        if ($record->product_id) {
                            return $record->product?->spd_internal_id ?? '—';
                        }
                        return $record->consumable?->part_number ?? '—';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('product', fn ($q) => $q->where('spd_internal_id', 'like', "%{$search}%"))
                            ->orWhereHas('consumable', fn ($q) => $q->where('part_number', 'like', "%{$search}%"));
                    }),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->state(function ($record): string {
                        if ($record->product_id) {
                            $p = $record->product;
                            return $p ? "{$p->brand} {$p->model}" : '—';
                        }
                        $c = $record->consumable;
                        return $c ? $c->name . ($c->brand ? " ({$c->brand})" : '') : '—';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('product', function ($q) use ($search): void {
                            $q->where('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%");
                        })->orWhereHas('consumable', function ($q) use ($search): void {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('brand', 'like', "%{$search}%");
                        });
                    })
                    ->wrap(),

                TextColumn::make('serie_lote')
                    ->label('Serie / Lote')
                    ->state(function ($record): string {
                        if ($record->product_id) {
                            return $record->product?->serial_number ?? '—';
                        }
                        return $record->consumable?->batch ?? '—';
                    })
                    ->toggleable(),

                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('sub_location')
                    ->label('Sub-ubicación')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('estatus')
                    ->label('Estatus')
                    ->state(function ($record): string {
                        if ($record->product_id) {
                            return $record->product?->inventory_status?->value ?? '—';
                        }
                        return $record->consumable?->inventory_status?->value ?? '—';
                    })
                    ->badge()
                    ->color(function ($state): string {
                        return match ($state) {
                            'disponible'    => 'success',
                            'rentado'       => 'warning',
                            'mantenimiento' => 'info',
                            'vendido'       => 'danger',
                            default         => 'gray',
                        };
                    }),

                TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('location_id')
                    ->label('Ubicación')
                    ->options(fn (): array => Location::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable(),

                SelectFilter::make('kind')
                    ->label('Tipo de ítem')
                    ->options([
                        'equipment'  => 'Equipos',
                        'consumable' => 'Consumibles',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'equipment'  => $query->whereNotNull('product_id'),
                            'consumable' => $query->whereNotNull('consumable_id'),
                            default      => $query,
                        };
                    }),

                Filter::make('low_stock')
                    ->label('Stock bajo mínimo')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('consumable_id')
                        ->whereHas('consumable', fn ($q) => $q->whereRaw('stock_quantity <= minimum_stock'))),
            ])
            ->defaultSort('location_id')
            ->striped()
            ->paginated([25, 50, 100]);
    }
}
