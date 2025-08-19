<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('traffic_admins')->insert([
            'admin_email' => 'admin@gmail.com',
            'admin_password' => Hash::make('admin123'),
            'admin_name' => 'Traffic Administrative',
            'code' => 408343,
            'status' => 'verified',
        ]);
    }
}
