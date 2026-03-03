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
            $table->enum('unit', ['pieza', 'caja', 'kit', 'litro', 'ml'])->default('pieza')->after('status');
            $table->string('sub_location')->nullable()->after('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropColumn([
                'unit',
                'sub_location',
            ]);
        });
    }
};
