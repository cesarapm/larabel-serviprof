<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EquipmentMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'client_id',
        'type',
        'date_out',
        'date_return',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_out' => 'date',
            'date_return' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
