<?php

// database/migrations/xxxx_xx_xx_create_vehicles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('vehicle_id');
            $table->string('vehicle_no')->unique();
            $table->string('vehicle_make');
            $table->string('vehicle_model');
            $table->string('vehicle_color');
            $table->string('vehicle_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
