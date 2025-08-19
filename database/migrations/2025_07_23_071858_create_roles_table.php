<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name')->unique(); // e.g. 'admin', 'enforcer'
            $table->timestamps();
        });

        $timestamp = now();

        DB::table('roles')->insert([
            ['role_name' => 'TRAFFIC ADMIN', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['role_name' => 'TRAFFIC ENFORCER', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
