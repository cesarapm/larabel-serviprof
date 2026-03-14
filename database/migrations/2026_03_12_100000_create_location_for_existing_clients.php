<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Crea una ubicación de tipo 'cliente' para todos los clientes que aún no tengan una.
     */
    public function up(): void
    {
        $clients = DB::table('clients')
            ->whereNotExists(function ($query) {
                $query->select('id')
                    ->from('locations')
                    ->whereColumn('locations.client_id', 'clients.id');
            })
            ->get(['id', 'name', 'company']);

        foreach ($clients as $client) {
            $locationName = $client->company
                ? "{$client->company} — {$client->name}"
                : $client->name;

            DB::table('locations')->insert([
                'name'       => $locationName,
                'type'       => 'cliente',
                'client_id'  => $client->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Elimina las ubicaciones creadas automáticamente para clientes (solo las de tipo 'cliente').
     * ⚠ Solo revierte registros creados por esta migración; no elimina ubicaciones de cliente
     * ingresadas manualmente antes de correr esta migración.
     */
    public function down(): void
    {
        // No es seguro eliminar ubicaciones que pudieran tener movimientos asociados;
        // se deja como no-op intencional.
    }
};
