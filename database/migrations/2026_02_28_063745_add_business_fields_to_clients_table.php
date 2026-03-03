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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('rfc', 13)->nullable()->after('name');
            $table->string('company')->nullable()->after('rfc');
            $table->string('contact_name')->nullable()->after('company');
            $table->string('department')->nullable()->after('contact_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'rfc',
                'company',
                'contact_name',
                'department',
            ]);
        });
    }
};
