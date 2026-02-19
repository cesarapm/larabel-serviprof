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
        Schema::table('products', function (Blueprint $table) {
            $table->string('spd_internal_id')->nullable()->unique()->after('serial_number');
            $table->unsignedBigInteger('current_counter_bw')->nullable()->after('spd_internal_id');
            $table->unsignedBigInteger('current_counter_color')->nullable()->after('current_counter_bw');
            $table->date('counter_read_at')->nullable()->after('current_counter_color');

            $table->enum('classification', ['renta', 'venta', 'refaccion', 'demo', 'taller'])->nullable()->after('inventory_status');
            $table->enum('commercial_condition', ['a1', 'a2', 'b', 'c'])->nullable()->after('classification');

            $table->decimal('acquisition_cost', 12, 2)->nullable()->after('commercial_condition');
            $table->string('supplier')->nullable()->after('acquisition_cost');
            $table->date('acquisition_date')->nullable()->after('supplier');

            $table->decimal('book_value', 12, 2)->nullable()->after('acquisition_date');
            $table->decimal('depreciation_amount', 12, 2)->nullable()->after('book_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'spd_internal_id',
                'current_counter_bw',
                'current_counter_color',
                'counter_read_at',
                'classification',
                'commercial_condition',
                'acquisition_cost',
                'supplier',
                'acquisition_date',
                'book_value',
                'depreciation_amount',
            ]);
        });
    }
};
