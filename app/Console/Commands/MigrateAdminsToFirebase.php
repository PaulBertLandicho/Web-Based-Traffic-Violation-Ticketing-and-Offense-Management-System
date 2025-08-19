<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;

class MigrateAdminsToFirebase extends Command
{
    protected $signature = 'firebase:migrate-admins';
    protected $description = 'Migrate all traffic_admins records to Firebase Realtime Database';

    public function handle()
    {
        // Resolve FirebaseService manually
        $firebaseService = app(FirebaseService::class);
        $firebaseDb = $firebaseService->getDatabase();

        // Fetch all traffic_admins from MySQL
        $admins = DB::table('traffic_admins')->get();

        if ($admins->isEmpty()) {
            $this->warn('No admin records found.');
            return;
        }

        $this->info("Found {$admins->count()} admins. Migrating...");

        foreach ($admins as $admin) {
            // Prepare data to push to Firebase
            $data = [
                'id'    => $admin->id,
                'name'  => $admin->admin_name,
                'email' => $admin->admin_email,
                'code' => $admin->code,
                'status' => $admin->status,
                'role' => $admin->role_id,
            ];

            // Save to Firebase under: traffic_admins/{id}
            $firebaseDb
                ->getReference('traffic_admins/' . $admin->id)
                ->set($data);

            $this->line("✔ Migrated admin ID: {$admin->id}");
        }

        $this->info('✅ All traffic_admins migrated to Firebase successfully.');
    }
}
