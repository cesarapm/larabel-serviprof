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
        Schema::create('equipment_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnUpdate()->nullOnDelete();
            $table->enum('type', ['entrada', 'salida', 'renta', 'venta', 'mantenimiento']);
            $table->date('date_out')->nullable();
            $table->date('date_return')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_movements');
    }
};
