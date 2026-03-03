<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->enum('status', ['nuevo', 'usado', 'renta', 'reparacion'])
                ->default('nuevo')
                ->after('model');
            $table->foreignId('location_id')
                ->nullable()
                ->after('supplier')
                ->constrained('locations')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn('status');
        });
    }
};
