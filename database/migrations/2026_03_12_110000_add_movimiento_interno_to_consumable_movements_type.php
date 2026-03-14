<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE consumable_movements MODIFY type ENUM('entrada', 'salida', 'ajuste', 'movimiento_interno') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE consumable_movements MODIFY type ENUM('entrada', 'salida', 'ajuste') NOT NULL");
    }
};
