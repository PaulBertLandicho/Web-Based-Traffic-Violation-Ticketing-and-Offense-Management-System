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
        Schema::create('traffic_violations', function (Blueprint $table) {
            $table->id('violation_id');
            $table->string('violation_type');      // e.g., "Speeding", "No Helmet", etc.
            $table->decimal('violation_amount', 10, 2); // e.g., 500.00
            $table->boolean('is_archived')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_violations');
    }
};
