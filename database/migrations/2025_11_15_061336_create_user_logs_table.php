<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLogsTable extends Migration
{
    public function up()
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->string('enforcer_id'); // match your traffic_enforcers.enforcer_id
            $table->string('action');
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Optional: if you want, you can add an index
            $table->index('enforcer_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_logs');
    }
}
