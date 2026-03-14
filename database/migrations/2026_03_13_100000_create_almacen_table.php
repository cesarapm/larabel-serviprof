<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Crea la tabla almacen para rastrear qué item está en qué ubicación
 * y con qué cantidad, permitiendo que un mismo consumible esté en
 * múltiples ubicaciones simultáneamente.
 *
 * Estructura:
 *  - product_id     → equipo (serial único, siempre 1 unidad, una ubicación)
 *  - consumable_id  → consumible (puede tener varias filas, una por ubicación)
 *  - location_id    → ubicación física
 *  - quantity       → cantidad en esa ubicación (siempre 1 para equipos)
 *
 * Después de crear esta tabla se migran los datos existentes desde
 * products.location_id y consumables.location_id + stock_quantity.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('consumable_id')
                ->nullable()
                ->constrained('consumables')
                ->cascadeOnDelete();

            $table->foreignId('location_id')
                ->constrained('locations')
                ->restrictOnDelete();

            // Cantidad en esta ubicación (1 para equipos, variable para consumibles)
            $table->unsignedInteger('quantity')->default(1);

            $table->string('sub_location')->nullable()->comment('Posición dentro de la ubicación: estante, caja, etc.');
            $table->text('notes')->nullable();

            $table->timestamps();

            // Un equipo solo puede aparecer una vez (tiene serial único)
            $table->unique(['product_id', 'location_id']);
            // Un consumible tiene máximo una fila por ubicación
            $table->unique(['consumable_id', 'location_id']);
        });

        // ─── Migrar datos existentes ──────────────────────────────────────────────

        // Equipos: crear una fila por producto que tenga location_id asignado
        if (Schema::hasColumn('products', 'location_id')) {
            DB::statement("
                INSERT INTO almacen (product_id, location_id, quantity, created_at, updated_at)
                SELECT id, location_id, 1, NOW(), NOW()
                FROM products
                WHERE location_id IS NOT NULL
            ");
        }

        // Consumibles: crear una fila por consumible que tenga location_id y stock > 0
        if (Schema::hasColumn('consumables', 'location_id')) {
            DB::statement("
                INSERT INTO almacen (consumable_id, location_id, quantity, sub_location, created_at, updated_at)
                SELECT id, location_id, GREATEST(stock_quantity, 0), sub_location, NOW(), NOW()
                FROM consumables
                WHERE location_id IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen');
    }
};
