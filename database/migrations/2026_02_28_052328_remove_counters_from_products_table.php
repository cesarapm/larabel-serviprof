<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'current_counter_bw',
                'current_counter_color',
                'counter_read_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('current_counter_bw')->nullable()->after('spd_internal_id');
            $table->unsignedInteger('current_counter_color')->nullable()->after('current_counter_bw');
            $table->date('counter_read_at')->nullable()->after('current_counter_color');
        });
    }
};
