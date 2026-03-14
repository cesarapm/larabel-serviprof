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
        static::created(function (ConsumableMovement $movement): void {
            self::applyAlmacenDelta($movement, +1);
            self::syncConsumableInventory($movement->consumable_id);
        });

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

        static::deleted(function (ConsumableMovement $movement): void {
            self::applyAlmacenDelta($movement, -1);
            self::syncConsumableInventory($movement->consumable_id);
        });
    }

    /**
     * Aplica (o revierte cuando $sign = -1) el delta en la tabla almacen
     * que corresponde a este movimiento.
     */
    private static function applyAlmacenDelta(ConsumableMovement $movement, int $sign): void
    {
        $id  = $movement->consumable_id;
        $loc = $movement->location_id;
        $qty = (int) $movement->quantity * $sign;

        match ($movement->type) {
            'entrada', 'ajuste' => $loc ? Almacen::adjustConsumableStock($id, $loc, +$qty) : null,
            'salida', 'vendido' => $loc
                ? Almacen::adjustConsumableStock($id, $loc, -$qty)
                : Almacen::greedyDeductConsumableStock($id, abs($qty)),
            'movimiento_interno' => (function () use ($movement, $sign, $id, $loc, $qty): void {
                $fromLoc = $movement->from_location_id;
                if ($fromLoc && $loc) {
                    if ($sign > 0) {
                        // Movimiento normal: de from_location_id → location_id
                        Almacen::moveConsumableStock($id, $fromLoc, $loc, abs($qty));
                    } else {
                        // Reversión al eliminar: deshacer el traslado
                        Almacen::moveConsumableStock($id, $loc, $fromLoc, abs($qty));
                    }
                }
            })(),
            default => null,
        };
    }

    protected $fillable = [
        'consumable_id',
        'client_id',
        'location_id',
        'from_location_id',
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

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
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

        // La fuente de verdad del stock actual es la tabla almacen.
        // applyAlmacenDelta la mantiene actualizada en cada movimiento,
        // por lo que no dependemos de tener todos los movimientos históricos.
        $stockQuantity = (int) Almacen::where('consumable_id', $consumableId)->sum('quantity');

        $stockReserved   = min((int) $consumable->stock_reserved, $stockQuantity);
        $inventoryStatus = $stockQuantity > 0 ? InventoryStatus::DISPONIBLE : InventoryStatus::VENDIDO;

        $consumable->update([
            'stock_quantity'   => $stockQuantity,
            'stock_reserved'   => $stockReserved,
            'inventory_status' => $inventoryStatus,
        ]);
    }

    private static function syncConsumableInventory(int $consumableId): void
    {
        self::recalculateConsumableInventory($consumableId);
    }
}
