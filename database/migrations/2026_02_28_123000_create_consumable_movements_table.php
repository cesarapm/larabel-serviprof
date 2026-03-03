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
        Schema::create('consumable_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumable_id')->constrained('consumables')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('type', ['entrada', 'salida', 'ajuste']);
            $table->unsignedInteger('quantity');
            $table->date('movement_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_movements');
    }
};
