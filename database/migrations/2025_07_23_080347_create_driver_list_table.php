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
        Schema::create('driver_list', function (Blueprint $table) {
            $table->id('driver_id');
            $table->string('license_id')->unique();
            $table->string('driver_name');
            $table->string('home_address');
            $table->string('contact_no');
            $table->date('license_issue_date');
            $table->date('license_expire_date');
            $table->date('date_of_birth');
            $table->string('license_type');
            $table->date('registered_at')->nullable();
            $table->string('status')->default('verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_list');
    }
};
