<?php

namespace App\Filament\Resources\EquipmentMovements\Pages;

use App\Filament\Resources\EquipmentMovements\EquipmentMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEquipmentMovement extends EditRecord
{
    protected static string $resource = EquipmentMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
