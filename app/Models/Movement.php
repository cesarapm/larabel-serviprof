<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $table = 'movements_view';

    protected $primaryKey = 'uid';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'current_counter_bw' => 'integer',
            'current_counter_color' => 'integer',
            'movement_date' => 'date',
            'counter_read_at' => 'date',
            'date_return' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
