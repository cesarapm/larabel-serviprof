<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('sub_location')->nullable()->after('name');
            $table->foreignId('client_id')->nullable()->after('type')->constrained('clients')->cascadeOnUpdate()->nullOnDelete();
        });

        DB::statement("ALTER TABLE locations MODIFY type ENUM('almacen_apodaca', 'taller', 'transito', 'cliente', 'baja_canibalizacion', 'demo_showroom', 'almacen', 'sucursal') NOT NULL");

        DB::table('locations')->where('type', 'almacen')->update(['type' => 'almacen_apodaca']);
        DB::table('locations')->where('type', 'sucursal')->update(['type' => 'demo_showroom']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('locations')
            ->whereIn('type', ['taller', 'transito', 'baja_canibalizacion', 'demo_showroom'])
            ->update(['type' => 'almacen']);

        DB::statement("ALTER TABLE locations MODIFY type ENUM('almacen', 'cliente', 'sucursal') NOT NULL");

        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['sub_location', 'client_id']);
        });
    }
};
