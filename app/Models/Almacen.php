<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tabla de almacén: relaciona cada ítem (equipo o consumible) con su
 * ubicación física y la cantidad almacenada en ese lugar.
 *
 * Reglas:
 *  - Un equipo (product_id) siempre tiene una sola fila con quantity = 1.
 *    Cuando se hace movimiento_interno, se cambia location_id de esa fila.
 *  - Un consumible (consumable_id) puede tener N filas, una por ubicación.
 *    La suma de todas las filas debe coincidir con consumables.stock_quantity.
 *
 * @property int         $id
 * @property int|null    $product_id
 * @property int|null    $consumable_id
 * @property int         $location_id
 * @property int         $quantity
 * @property string|null $sub_location
 * @property string|null $notes
 */
class Almacen extends Model
{
    protected $table = 'almacen';

    protected $fillable = [
        'product_id',
        'consumable_id',
        'location_id',
        'quantity',
        'sub_location',
        'notes',
    ];

    // ─── Relaciones ──────────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    /**
     * Ajusta el stock de un consumible en una ubicación específica.
     * Crea la fila si no existe. Elimina la fila si quantity llega a 0 o menos.
     */
    public static function adjustConsumableStock(int $consumableId, int $locationId, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        $row = self::firstOrNew([
            'consumable_id' => $consumableId,
            'location_id'   => $locationId,
        ]);

        $row->quantity  = max(0, ($row->quantity ?? 0) + $delta);
        $row->product_id = null;

        if ($row->quantity === 0) {
            if ($row->exists) {
                $row->delete();
            }
        } else {
            $row->save();
        }
    }

    /**
     * Descuenta stock de un consumible recorriendo sus filas de mayor a menor cantidad.
     * Se usa cuando una salida no especifica ubicación de origen.
     */
    public static function greedyDeductConsumableStock(int $consumableId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $rows = self::where('consumable_id', $consumableId)
            ->orderByDesc('quantity')
            ->get();

        $remaining = $quantity;

        foreach ($rows as $row) {
            if ($remaining <= 0) {
                break;
            }

            $deduct        = min($remaining, (int) $row->quantity);
            $row->quantity -= $deduct;
            $remaining    -= $deduct;

            if ($row->quantity <= 0) {
                $row->delete();
            } else {
                $row->save();
            }
        }
    }

    /**
     * Mueve N unidades de un consumible de una ubicación a otra.
     * Para: consumable movimiento_interno.
     */
    public static function moveConsumableStock(
        int $consumableId,
        int $fromLocationId,
        int $toLocationId,
        int $quantity
    ): void {
        self::adjustConsumableStock($consumableId, $fromLocationId, -$quantity);
        self::adjustConsumableStock($consumableId, $toLocationId, +$quantity);
    }

    /**
     * Mueve un equipo a una nueva ubicación (actualiza la fila existente).
     * Para: equipment movimiento_interno.
     */
    public static function moveProduct(int $productId, int $toLocationId): void
    {
        self::updateOrCreate(
            ['product_id' => $productId],
            ['location_id' => $toLocationId, 'quantity' => 1, 'consumable_id' => null]
        );
    }
}
