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
        Schema::table('traffic_enforcers', function (Blueprint $table) {
            $table->boolean('first_violation_warning')
                ->default(0)
                ->after('is_locked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traffic_enforcers', function (Blueprint $table) {
            $table->dropColumn('first_violation_warning');
        });
    }
};
