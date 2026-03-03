<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnel';

    protected $fillable = [
        'name',
        'position',
        'phone',
        'email',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function equipmentMovements(): HasMany
    {
        return $this->hasMany(EquipmentMovement::class);
    }

    public function consumableMovements(): HasMany
    {
        return $this->hasMany(ConsumableMovement::class);
    }
}
