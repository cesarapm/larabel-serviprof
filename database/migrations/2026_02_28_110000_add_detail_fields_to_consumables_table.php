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
            $table->enum('type', ['refaccion', 'tinta', 'toner', 'otras'])->default('otras')->after('id');
            $table->string('part_number')->nullable()->after('name');
            $table->string('serial_number')->nullable()->after('part_number');
            $table->string('brand')->nullable()->after('serial_number');
            $table->string('model')->nullable()->after('brand');
            $table->text('notes')->nullable()->after('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'part_number',
                'serial_number',
                'brand',
                'model',
                'notes',
            ]);
        });
    }
};
