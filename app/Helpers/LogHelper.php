<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\UserLog;

class LogHelper
{
    public static function record($enforcerId, $action, $details = null)
    {
        $logId = DB::table('user_logs')->insertGetId([
            'enforcer_id' => $enforcerId,
            'action'      => $action,
            'details'     => $details,
            'ip_address'  => request()->ip(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Optionally push to Firebase
        self::pushToFirebase($enforcerId, $action, $details);

        return $logId;
    }

    protected static function pushToFirebase($enforcerId, $action, $details = null)
    {
        $firebaseUrl = 'https://traffic-violation-system-79ff7-default-rtdb.asia-southeast1.firebasedatabase.app/admin_user_logs.json';

        $data = [
            'title' => 'User Activity',
            'message' => "Enforcer #$enforcerId $action",
            'user_id' => $enforcerId,
            'details' => $details,
            'created_at' => now()->toIso8601String(),
            'status' => 'unread'
        ];

        $ch = curl_init($firebaseUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
