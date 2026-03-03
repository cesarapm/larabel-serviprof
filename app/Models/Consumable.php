<?php

namespace App\Models;

use App\Enums\EquipmentStatus;
use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'part_number',
        'serial_number',
        'brand',
        'model',
        'status',
        'inventory_status',
        'unit',
        'stock_quantity',
        'minimum_stock',
        'stock_reserved',
        'batch',
        'supplier',
        'location_id',
        'sub_location',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => EquipmentStatus::class,
            'inventory_status' => InventoryStatus::class,
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ConsumableMovement::class);
    }
}
