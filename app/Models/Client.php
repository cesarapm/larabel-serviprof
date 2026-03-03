<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'rfc',
        'company',
        'contact_name',
        'department',
    ];

    public function equipmentMovements(): HasMany
    {
        return $this->hasMany(EquipmentMovement::class);
    }
}
