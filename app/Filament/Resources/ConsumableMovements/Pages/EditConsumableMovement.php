<?php

namespace App\Filament\Resources\ConsumableMovements\Pages;

use App\Filament\Resources\ConsumableMovements\ConsumableMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConsumableMovement extends EditRecord
{
    protected static string $resource = ConsumableMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
