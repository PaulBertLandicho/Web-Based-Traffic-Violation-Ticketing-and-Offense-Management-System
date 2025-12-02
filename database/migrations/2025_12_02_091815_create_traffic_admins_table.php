<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('traffic_admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('admin_email')->unique();
            $table->string('admin_password');
            $table->string('admin_name');
            $table->text('status');
            $table->unsignedBigInteger('role_id');
            $table->mediumInteger('code');
            $table->timestamps();

            $table->foreign('role_id')
                ->references('role_id')
                ->on('roles')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_admins');
    }
};
