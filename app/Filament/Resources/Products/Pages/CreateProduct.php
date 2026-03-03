<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\EquipmentMovement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    /** @var array{current_counter_bw:mixed,current_counter_color:mixed,counter_read_at:mixed,personnel_id:mixed} */
    private array $initialCounters = [
        'current_counter_bw' => null,
        'current_counter_color' => null,
        'counter_read_at' => null,
        'personnel_id' => null,
    ];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->initialCounters = [
            'current_counter_bw' => $data['current_counter_bw'] ?? null,
            'current_counter_color' => $data['current_counter_color'] ?? null,
            'counter_read_at' => $data['counter_read_at'] ?? null,
            'personnel_id' => $data['personnel_id'] ?? null,
        ];

        return Arr::except($data, [
            'current_counter_bw',
            'current_counter_color',
            'counter_read_at',
            'personnel_id',
        ]);
    }

    protected function afterCreate(): void
    {
        EquipmentMovement::create([
            'product_id' => $this->record->id,
            'client_id' => null,
            'location_id' => $this->record->location_id,
            'personnel_id' => $this->initialCounters['personnel_id'],
            'type' => 'entrada',
            'current_counter_bw' => $this->initialCounters['current_counter_bw'],
            'current_counter_color' => $this->initialCounters['current_counter_color'],
            'counter_read_at' => $this->initialCounters['counter_read_at'],
            'date_out' => $this->record->entry_date,
            'date_return' => null,
            'notes' => 'Alta inicial de equipo',
        ]);
    }
}
