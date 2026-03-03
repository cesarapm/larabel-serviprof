<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EquipmentMovement extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(fn (EquipmentMovement $movement) => self::syncProductInventoryStatus($movement->product_id));

        static::updated(function (EquipmentMovement $movement): void {
            self::syncProductInventoryStatus($movement->product_id);

            if ($movement->wasChanged('product_id')) {
                /** @var int|null $originalProductId */
                $originalProductId = $movement->getOriginal('product_id');

                if ($originalProductId) {
                    self::syncProductInventoryStatus($originalProductId);
                }
            }
        });

        static::deleted(fn (EquipmentMovement $movement) => self::syncProductInventoryStatus($movement->product_id));
    }

    protected $fillable = [
        'product_id',
        'client_id',
        'location_id',
        'personnel_id',
        'type',
        'current_counter_bw',
        'current_counter_color',
        'counter_read_at',
        'date_out',
        'date_return',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'current_counter_bw' => 'integer',
            'current_counter_color' => 'integer',
            'counter_read_at' => 'date',
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class);
    }

    public static function recalculateProductInventoryStatus(int $productId): void
    {
        $product = Product::query()->find($productId);

        if (! $product) {
            return;
        }

        $latestMovement = self::query()
            ->where('product_id', $productId)
            ->latest('created_at')
            ->latest('id')
            ->first();

        if (! $latestMovement) {
            $product->update(['inventory_status' => InventoryStatus::DISPONIBLE]);

            return;
        }

        $movementType = strtolower(trim((string) $latestMovement->type));

        $status = match ($movementType) {
            'entrada' => InventoryStatus::DISPONIBLE,
            'salida', 'renta' => InventoryStatus::RENTADO,
            'venta' => InventoryStatus::VENDIDO,
            'mantenimiento' => InventoryStatus::MANTENIMIENTO,
            default => InventoryStatus::DISPONIBLE,
        };

        if ($latestMovement->date_return && $movementType !== 'venta') {
            $status = InventoryStatus::DISPONIBLE;
        }

        $payload = ['inventory_status' => $status];

        if ($latestMovement->location_id) {
            $payload['location_id'] = $latestMovement->location_id;
        }

        $product->update($payload);
    }

    private static function syncProductInventoryStatus(int $productId): void
    {
        self::recalculateProductInventoryStatus($productId);
    }
}
