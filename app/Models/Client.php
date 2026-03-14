<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    protected static function booted(): void
    {
        // Al crear un cliente, se genera automáticamente su ubicación de tipo 'cliente'
        static::created(function (Client $client) {
            Location::create([
                'name'      => self::buildLocationName($client),
                'type'      => 'cliente',
                'client_id' => $client->id,
            ]);
        });

        // Si cambia el nombre o empresa del cliente, sincronizar el nombre de su ubicación
        static::updated(function (Client $client) {
            if ($client->wasChanged(['name', 'company'])) {
                $client->location()->update([
                    'name' => self::buildLocationName($client),
                ]);
            }
        });
    }

    /**
     * Construye el nombre de la ubicación del cliente.
     * Si tiene empresa: "Empresa — Nombre contacto"
     * Si no tiene empresa: "Nombre contacto"
     */
    private static function buildLocationName(Client $client): string
    {
        return $client->company
            ? "{$client->company} — {$client->name}"
            : $client->name;
    }

    /** Ubicación asignada a este cliente (para envío de equipos/consumibles). */
    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
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
