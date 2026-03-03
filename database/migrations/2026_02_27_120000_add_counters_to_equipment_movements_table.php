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
            $table->unsignedInteger('current_counter_bw')->nullable()->after('type');
            $table->unsignedInteger('current_counter_color')->nullable()->after('current_counter_bw');
            $table->date('counter_read_at')->nullable()->after('current_counter_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_movements', function (Blueprint $table) {
            $table->dropColumn([
                'current_counter_bw',
                'current_counter_color',
                'counter_read_at',
            ]);
        });
    }
};
