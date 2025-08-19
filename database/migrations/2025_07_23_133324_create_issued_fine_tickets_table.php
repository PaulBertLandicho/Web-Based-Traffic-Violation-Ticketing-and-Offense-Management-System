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
        Schema::create('issued_fine_tickets', function (Blueprint $table) {
            $table->id('ref_no');
            $table->string('enforcer_id');
            $table->string('license_id');
            $table->string('vehicle_no');
            $table->string('place');
            $table->date('issued_date');
            $table->string('issued_time');
            $table->date('due_date');
            $table->boolean('penalty_applied')->default(false);
            $table->string('violation_type');
            $table->decimal('total_amount', 10, 2);
            $table->integer('offense_number')->default(1);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_date')->nullable();
            $table->string('secure_token')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issued_fine_tickets');
    }
};
