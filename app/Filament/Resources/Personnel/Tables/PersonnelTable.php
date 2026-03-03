<?php

namespace App\Filament\Resources\Personnel\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Exports\PersonnelExporter;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;

class PersonnelTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('position')
                    ->label('Puesto')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(PersonnelExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(PersonnelExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
