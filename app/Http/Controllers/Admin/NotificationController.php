<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function notif()
    {
        // This is block to avoid auto login 
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        // Get all enforcers
        $enforcers = DB::table('traffic_enforcers')
            ->where('is_archived', 0)
            ->get();
        $notifications = Notification::with('enforcer')->orderBy('created_at', 'desc')->get();
        return view('admin.notifications', compact('notifications', 'enforcers'));
    }

    public function destroy($id)
    {
        $notice = Notification::findOrFail($id);
        $notice->delete();

        return redirect()->route('admin.notifications')->with('success', 'Notification deleted successfully.');
    }

    public function ajax()
    {
        $notifications = DB::table('notifications')
            ->leftJoin('traffic_enforcers', 'notifications.enforcer_id', '=', 'traffic_enforcers.enforcer_id')
            ->select(
                'notifications.id',
                'notifications.title',
                'notifications.message',
                'notifications.is_read',
                'notifications.created_at',
                'notifications.enforcer_id',
                'traffic_enforcers.enforcer_name'
            )
            ->orderBy('notifications.created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }
}
