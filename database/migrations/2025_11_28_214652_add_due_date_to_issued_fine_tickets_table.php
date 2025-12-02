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
        Schema::table('issued_fine_tickets', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('expire_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issued_fine_tickets', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
