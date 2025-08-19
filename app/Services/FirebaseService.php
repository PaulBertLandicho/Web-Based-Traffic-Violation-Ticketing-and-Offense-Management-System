<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Exception;

class FirebaseService
{
    protected Database $database;

    public function __construct()
    {
        $serviceAccountPath = base_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($serviceAccountPath)) {
            throw new Exception("❌ Firebase credentials file not found at: {$serviceAccountPath}");
        }

        if (is_dir($serviceAccountPath)) {
            throw new Exception("❌ Expected a file, but found a directory at: {$serviceAccountPath}");
        }

        $factory = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));

        $this->database = $factory->createDatabase();
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    // public function getAllDrivers()
    // {
    //     return $this->getDatabase()->getReference('driver')->getValue();
    // }
}
