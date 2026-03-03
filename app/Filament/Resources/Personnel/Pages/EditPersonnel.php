<?php

namespace App\Filament\Resources\Personnel\Pages;

use App\Filament\Resources\Personnel\PersonnelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPersonnel extends EditRecord
{
    protected static string $resource = PersonnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
