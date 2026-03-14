<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE equipment_movements MODIFY type ENUM('entrada', 'salida', 'renta', 'venta', 'mantenimiento', 'movimiento_interno') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE equipment_movements MODIFY type ENUM('entrada', 'salida', 'renta', 'venta', 'mantenimiento') NOT NULL");
    }
};
