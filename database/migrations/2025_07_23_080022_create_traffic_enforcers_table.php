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
        Schema::create('traffic_enforcers', function (Blueprint $table) {
            $table->string('enforcer_id')->primary();
            $table->string('enforcer_email')->unique();
            $table->string('enforcer_password');
            $table->string('enforcer_name');
            $table->string('assigned_area');
            $table->string('contact_no');
            $table->string('gender');
            $table->date('registered_at');
            $table->boolean('is_locked')->default(0);
            $table->timestamp('password_updated')->useCurrent()->nullable();
            $table->unsignedBigInteger('role_id');
            $table->mediumInteger('code')->nullable();
            $table->boolean('is_archived')->default(0);

            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');

            // ✅ Add timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_enforcers');
    }
};
