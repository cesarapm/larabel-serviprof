<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elimina location_id de la tabla products y
 * location_id + sub_location de la tabla consumables.
 *
 * Estos datos ahora viven en la tabla almacen.
 * Ejecutar DESPUÉS de confirmar que 2026_03_13_100000_create_almacen_table
 * migró los datos correctamente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Puede haber FK constraint con nombre generado automáticamente
            try {
                $table->dropForeign(['location_id']);
            } catch (\Throwable) {
                // Ignorar si no existe la FK con ese nombre
            }
            $table->dropColumn('location_id');
        });

        Schema::table('consumables', function (Blueprint $table) {
            try {
                $table->dropForeign(['location_id']);
            } catch (\Throwable) {
                // Ignorar si no existe la FK con ese nombre
            }
            $table->dropColumn('location_id');
            $table->dropColumn('sub_location');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
        });

        Schema::table('consumables', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('sub_location')->nullable();
        });
    }
};
