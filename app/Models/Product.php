<?php

namespace App\Models;

use App\Enums\EquipmentStatus;
use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'brand',
        'model',
        'serial_number',
        'spd_internal_id',
        'status',
        'inventory_status',
        'classification',
        'commercial_condition',
        'acquisition_cost',
        'supplier',
        'acquisition_date',
        'book_value',
        'depreciation_amount',
        'entry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => EquipmentStatus::class,
            'inventory_status' => InventoryStatus::class,
            'entry_date' => 'date',
            'acquisition_date' => 'date',
            'acquisition_cost' => 'decimal:2',
            'book_value' => 'decimal:2',
            'depreciation_amount' => 'decimal:2',
        ];
    }

    /** Entrada en la tabla almacen que indica la ubicación actual del equipo. */
    public function almacen(): HasOne
    {
        return $this->hasOne(Almacen::class, 'product_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(EquipmentMovement::class);
    }
}
