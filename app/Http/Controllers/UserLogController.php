<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLog;

class UserLogController extends Controller
{
    public function index()
    {
        $logs = UserLog::with('enforcer')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.user_logs', compact('logs'));
    }
}
