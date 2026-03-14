<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega from_location_id a consumable_movements.
 *
 * Necesario para el tipo movimiento_interno en consumibles:
 * se necesita saber de qué ubicación SALE la cantidad para poder
 * decrementar la fila correcta en la tabla almacen.
 *
 * Ejemplo: mover 5 tóneres de Almacén Apodaca → Sucursal Norte:
 *   from_location_id = Almacén Apodaca (origen, se decrementa)
 *   location_id      = Sucursal Norte  (destino, se incrementa)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumable_movements', function (Blueprint $table) {
            $table->foreignId('from_location_id')
                ->nullable()
                ->after('location_id')
                ->constrained('locations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('consumable_movements', function (Blueprint $table) {
            $table->dropForeign(['from_location_id']);
            $table->dropColumn('from_location_id');
        });
    }
};
