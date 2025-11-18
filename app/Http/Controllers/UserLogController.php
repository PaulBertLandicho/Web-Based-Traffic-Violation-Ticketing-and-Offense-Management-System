<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLog;
use App\Models\AdminLog;

class UserLogController extends Controller
{
    public function index()
    {
        // Fetch enforcer logs
        $userLogs = UserLog::with('enforcer')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Fetch admin logs
        $adminLogs = AdminLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Pass both to the view
        return view('admin.user_logs', compact('userLogs', 'adminLogs'));
    }
}
