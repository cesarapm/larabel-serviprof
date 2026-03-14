<?php

namespace App\Models;

use App\Enums\EquipmentStatus;
use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'stock_quantity',   // Total global (suma de todas las filas en almacen)
        'minimum_stock',
        'stock_reserved',
        'batch',
        'supplier',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => EquipmentStatus::class,
            'inventory_status' => InventoryStatus::class,
        ];
    }

    /** Filas en tabla almacen: una por ubicación donde haya stock */
    public function almacen(): HasMany
    {
        return $this->hasMany(Almacen::class, 'consumable_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ConsumableMovement::class);
    }
}
