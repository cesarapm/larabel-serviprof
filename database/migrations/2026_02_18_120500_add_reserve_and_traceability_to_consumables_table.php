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
            $table->unsignedInteger('stock_reserved')->default(0)->after('minimum_stock');
            $table->string('batch')->nullable()->after('stock_reserved');
            $table->string('supplier')->nullable()->after('batch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropColumn([
                'stock_reserved',
                'batch',
                'supplier',
            ]);
        });
    }
};
