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
        Schema::create('enforcer_violations', function (Blueprint $table) {
            $table->id();
            $table->string('enforcer_id'); // Foreign key to traffic_enforcers
            $table->string('violation_type');
            $table->text('details')->nullable();
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'settled'])->default('pending');
            $table->timestamp('date_issued')->useCurrent();
            $table->timestamps();

            $table->foreign('enforcer_id')->references('enforcer_id')->on('traffic_enforcers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enforcer_violations');
    }
};
