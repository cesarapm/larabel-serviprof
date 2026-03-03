<?php

namespace App\Filament\Resources\ConsumableMovements\Pages;

use App\Filament\Resources\ConsumableMovements\ConsumableMovementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsumableMovements extends ListRecords
{
    protected static string $resource = ConsumableMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
