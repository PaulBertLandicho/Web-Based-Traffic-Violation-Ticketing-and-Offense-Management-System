<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('admin_id')->nullable(); // admin_id INT
            $table->string('action', 255); // action VARCHAR(255)
            $table->string('ip_address', 100)->nullable(); // ip_address VARCHAR(100)
            $table->text('user_agent')->nullable(); // user_agent TEXT
            $table->timestamp('created_at')->useCurrent(); // created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
