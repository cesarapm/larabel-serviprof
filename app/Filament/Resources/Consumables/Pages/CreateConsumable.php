<?php

namespace App\Filament\Resources\Consumables\Pages;

use App\Filament\Resources\Consumables\ConsumableResource;
use App\Models\ConsumableMovement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateConsumable extends CreateRecord
{
    protected static string $resource = ConsumableResource::class;

    private ?int $initialPersonnelId = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->initialPersonnelId = isset($data['personnel_id']) ? (int) $data['personnel_id'] : null;

        return Arr::except($data, ['personnel_id']);
    }

    protected function afterCreate(): void
    {
        ConsumableMovement::create([
            'consumable_id' => $this->record->id,
            'client_id' => null,
            'location_id' => $this->record->location_id,
            'personnel_id' => $this->initialPersonnelId,
            'type' => 'entrada',
            'quantity' => (int) $this->record->stock_quantity,
            'movement_date' => now()->toDateString(),
            'notes' => 'Alta inicial de consumible',
        ]);
    }
}
