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
        Schema::table('equipment_movements', function (Blueprint $table) {
            $table->foreignId('location_id')
                ->nullable()
                ->after('client_id')
                ->constrained('locations')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('personnel_id')
                ->nullable()
                ->after('location_id')
                ->constrained('personnel')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('consumable_movements', function (Blueprint $table) {
            $table->foreignId('client_id')
                ->nullable()
                ->after('consumable_id')
                ->constrained('clients')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->after('client_id')
                ->constrained('locations')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('personnel_id')
                ->nullable()
                ->after('location_id')
                ->constrained('personnel')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumable_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('personnel_id');
            $table->dropConstrainedForeignId('location_id');
            $table->dropConstrainedForeignId('client_id');
        });

        Schema::table('equipment_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('personnel_id');
            $table->dropConstrainedForeignId('location_id');
        });
    }
};
