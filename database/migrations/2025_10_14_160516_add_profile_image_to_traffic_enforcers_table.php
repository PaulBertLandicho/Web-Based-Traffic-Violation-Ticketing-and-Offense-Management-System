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
            $table->string('profile_image')->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('traffic_enforcers', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });
    }
};
