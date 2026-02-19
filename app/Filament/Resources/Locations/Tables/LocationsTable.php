<?php

namespace App\Filament\Resources\Locations\Tables;

use App\Filament\Exports\LocationExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sub_location')
                    ->label('Sub-ubicación')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(LocationExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(LocationExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
