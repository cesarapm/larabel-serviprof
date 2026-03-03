<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumableMovement extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(fn (ConsumableMovement $movement) => self::syncConsumableInventory($movement->consumable_id));

        static::updated(function (ConsumableMovement $movement): void {
            self::syncConsumableInventory($movement->consumable_id);

            if ($movement->wasChanged('consumable_id')) {
                /** @var int|null $originalConsumableId */
                $originalConsumableId = $movement->getOriginal('consumable_id');

                if ($originalConsumableId) {
                    self::syncConsumableInventory($originalConsumableId);
                }
            }
        });

        static::deleted(fn (ConsumableMovement $movement) => self::syncConsumableInventory($movement->consumable_id));
    }

    protected $fillable = [
        'consumable_id',
        'client_id',
        'location_id',
        'personnel_id',
        'type',
        'quantity',
        'movement_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'movement_date' => 'date',
        ];
    }

    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class);
    }

    public static function recalculateConsumableInventory(int $consumableId): void
    {
        $consumable = Consumable::query()->find($consumableId);

        if (! $consumable) {
            return;
        }

        $balance = (int) self::query()
            ->where('consumable_id', $consumableId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'entrada' THEN quantity WHEN type = 'salida' THEN -quantity WHEN type = 'ajuste' THEN quantity ELSE 0 END), 0) AS balance")
            ->value('balance');

        $stockQuantity = max(0, $balance);
        $stockReserved = min((int) $consumable->stock_reserved, $stockQuantity);
        $inventoryStatus = $stockQuantity > 0 ? InventoryStatus::DISPONIBLE : InventoryStatus::VENDIDO;

        $consumable->update([
            'stock_quantity' => $stockQuantity,
            'stock_reserved' => $stockReserved,
            'inventory_status' => $inventoryStatus,
        ]);
    }

    private static function syncConsumableInventory(int $consumableId): void
    {
        self::recalculateConsumableInventory($consumableId);
    }
}
