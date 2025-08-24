<?php

namespace App\Http\Controllers\Enforcer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\TrafficEnforcer;
use App\Services\FirebaseService;

class EnforcerController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function enforcer()
    {
        return view('enforcer.enforcer-login');
    }

    // Show Add Traffic Enforcer Form
    public function create()
    {
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        return view('admin.add_enforcer');
    }
    // Handle login logic
    public function login(Request $request)
    {
        $request->validate([
            'enforcer_id' => 'required',
            'enforcer_password' => 'required'
        ]);

        $enforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', $request->enforcer_id)
            ->first();

        if ($enforcer && Hash::check($request->enforcer_password, $enforcer->enforcer_password)) {
            // If the account is locked, force logout and redirect with error
            if ($enforcer->is_locked) {
                Session::flush();
                return redirect()->route('enforcer.login')->with('error', 'Your account is locked.');
            }

            // Store session if not locked
            Session::put('enforcer_id', $enforcer->enforcer_id);
            Session::put('enforcer_name', $enforcer->enforcer_name);
            Session::put('enforcer_email', $enforcer->enforcer_email);

            // Get Role
            $role = DB::table('roles')->where('role_id', $enforcer->role_id)->value('role_name');
            Session::put('role', $role);

            return redirect()->route('enforcer.enforcer-dashboard');
        }

        return redirect()->route('enforcer.login')->with('error', 'Invalid ID or Password!');
    }

    public function checkLock(Request $request)
    {
        $enforcer = TrafficEnforcer::where('enforcer_id', session('enforcer_id'))->first();

        if (!$enforcer) {
            return response('not_found');
        }

        return response($enforcer->is_locked ? 'locked' : 'unlocked');
    }


    public function enforcerDashboard()
    {
        if (!session()->has('enforcer_id')) {
            return redirect()->route('enforcer.login')->with('error', 'Please login first.');
        }

        $enforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', session('enforcer_id'))
            ->first();

        if ($enforcer && $enforcer->is_locked) {
            session()->flush();
            return redirect()->route('enforcer.login')->with('error', 'Your account is locked.');
        }

        $reportedFines = DB::table('issued_fine_tickets')
            ->where('enforcer_id', session('enforcer_id'))
            ->get();

        $fineCount = $reportedFines->count();
        $fineAmount = $reportedFines->sum('total_amount');
        $enforcerName = $enforcer->enforcer_name ?? 'N/A';
        $assignedArea = $enforcer->assigned_area ?? 'N/A';
        // Define monthly total amount variables
        $monthlyCounts = [];

        for ($month = 1; $month <= 12; $month++) {
            $count = DB::table('issued_fine_tickets')
                ->where('enforcer_id', session('enforcer_id')) // 👈 ADD THIS
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->count('ref_no');

            $monthlyCounts[$month] = $count;
        }

        // Define monthly total fine amount per month
        $monthlyTotals = [];

        for ($month = 1; $month <= 12; $month++) {
            $total = DB::table('issued_fine_tickets')
                ->where('enforcer_id', session('enforcer_id')) // 👈 ADD THIS
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount');

            $monthlyTotals[$month] = $total;
        }


        // Prepare data to return
        return view('enforcer.enforcer-dashboard', array_merge([
            'fineCount' => $fineCount,
            'fineAmount' => $fineAmount,
            'janTotal' => $monthlyTotals[1],
            'febTotal' => $monthlyTotals[2],
            'marchTotal' => $monthlyTotals[3],
            'aprilTotal' => $monthlyTotals[4],
            'mayTotal' => $monthlyTotals[5],
            'juneTotal' => $monthlyTotals[6],
            'julyTotal' => $monthlyTotals[7],
            'augustTotal' => $monthlyTotals[8],
            'sepTotal' => $monthlyTotals[9],
            'octTotal' => $monthlyTotals[10],
            'novTotal' => $monthlyTotals[11],
            'decTotal' => $monthlyTotals[12],
            'enforcerName' => $enforcerName,
            'assignedArea' => $assignedArea,
            // For Count 
            'janCount' => $monthlyCounts[1],
            'febCount' => $monthlyCounts[2],
            'marchCount' => $monthlyCounts[3],
            'aprilCount' => $monthlyCounts[4],
            'mayCount' => $monthlyCounts[5],
            'juneCount' => $monthlyCounts[6],
            'julyCount' => $monthlyCounts[7],
            'augustCount' => $monthlyCounts[8],
            'sepCount' => $monthlyCounts[9],
            'octCount' => $monthlyCounts[10],
            'novCount' => $monthlyCounts[11],
            'decCount' => $monthlyCounts[12],
        ], $monthlyCounts));
    }


    // Store Traffic Enforcer
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enforcerid' => 'required|unique:traffic_enforcers,enforcer_id',
            'enforceremail' => 'required|email|unique:traffic_enforcers,enforcer_email',
            'enforcerpassword' => 'required|min:6',
            'enforcerpasswordconfirm' => 'required|same:enforcerpassword',
            'enforcername' => 'required',
            'assignedarea' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ✅ Get role_id once
        $roleId = DB::table('roles')->where('role_name', 'traffic enforcer')->value('role_id');

        // ✅ Ensure role_id is not null
        if (!$roleId) {
            return redirect()->back()->with('error', 'Enforcer role not found. Please check roles table.');
        }

        // ✅ Use correct variable for Firebase ID
        $enforcerId = $request->enforcerid;

        DB::table('traffic_enforcers')->insert([
            'enforcer_id' => $enforcerId,
            'enforcer_email' => $request->enforceremail,
            'enforcer_password' => Hash::make($request->enforcerpassword),
            'enforcer_name' => $request->enforcername,
            'assigned_area' => $request->assignedarea,
            'registered_at' => now()->toDateString(),
            'code' => random_int(100000, 999999),
            'is_locked' => false,
            'role_id' => $roleId,
        ]);

        // ✅ Save to Firebase
        $this->firebase->getDatabase()
            ->getReference('traffic_enforcers/' . $enforcerId)
            ->set([
                'enforcer_id' => $enforcerId,
                'enforcer_email' => $request->enforceremail,
                'enforcer_password' => Hash::make($request->enforcerpassword),
                'enforcer_name' => $request->enforcername,
                'assigned_area' => $request->assignedarea,
                'registered_at' => now()->toDateString(),
                'code' => random_int(100000, 999999),
                'is_locked' => false,
            ]);

        return redirect()->route('enforcers.create')->with('success', 'Traffic Enforcer added successfully!');
    }

    public function index()
    {
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        // Get all enforcers
        $enforcers = DB::table('traffic_enforcers')->get();

        // Get fine stats per enforcer
        $fineStats = DB::table('issued_fine_tickets')
            ->join('traffic_enforcers as enforcers', 'issued_fine_tickets.enforcer_id', '=', 'enforcers.enforcer_id')
            ->select(
                'issued_fine_tickets.enforcer_id',
                DB::raw('COUNT(issued_fine_tickets.ref_no) as reported_fine_count'),
                DB::raw('SUM(issued_fine_tickets.total_amount) as reported_fine_amount')
            )
            ->groupBy('issued_fine_tickets.enforcer_id')
            ->pluck('reported_fine_count', 'issued_fine_tickets.enforcer_id')
            ->merge(
                DB::table('issued_fine_tickets')
                    ->select(
                        'enforcer_id',
                        DB::raw('SUM(total_amount) as reported_fine_amount')
                    )
                    ->groupBy('enforcer_id')
                    ->pluck('reported_fine_amount', 'enforcer_id')
            );

        // Alternatively, better: combine both into one mapping by enforcer_id:
        $fineStats = DB::table('issued_fine_tickets')
            ->select(
                'enforcer_id',
                DB::raw('COUNT(ref_no) as reported_fine_count'),
                DB::raw('SUM(total_amount) as reported_fine_amount')
            )
            ->groupBy('enforcer_id')
            ->get()
            ->keyBy('enforcer_id');

        return view('admin.view_all_enforcers', compact(
            'enforcers',
            'fineStats',
        ));
    }

    public function toggleLock(Request $request)
    {
        $id = $request->input('id');

        $enforcer = DB::table('traffic_enforcers')->where('enforcer_id', $id)->first();

        if ($enforcer) {
            $newStatus = $enforcer->is_locked ? 0 : 1;

            DB::table('traffic_enforcers')
                ->where('enforcer_id', $id)
                ->update(['is_locked' => $newStatus]);

            return response()->json(['success' => true, 'status' => $newStatus]);
        }

        return response()->json(['success' => false]);
    }

    public function getEnforcerDetails(Request $request)
    {
        $id = $request->id; // must match the AJAX key 'id'

        $enforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', $id)
            ->first();

        if ($enforcer) {
            return response()->json(['enforcer' => $enforcer]);
        }
        return response()->json(['error' => 'Enforcer details not found.']);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'enforcer_id' => 'required',
            'enforcer_email' => 'nullable|email',
            'enforcer_name' => 'required|string',
            'assigned_area' => 'required|string',
        ]);

        DB::table('traffic_enforcers')
            ->where('enforcer_id', $validated['enforcer_id'])
            ->update([
                'enforcer_email' => $validated['enforcer_email'],
                'enforcer_name' => $validated['enforcer_name'],
                'assigned_area' => $validated['assigned_area']
            ]);

        // Update in Firebase
        $this->firebase->getDatabase()
            ->getReference('traffic_enforcers/' . $validated['enforcer_id'])
            ->update([
                'enforcer_email' => $validated['enforcer_email'],
                'enforcer_name' => $validated['enforcer_name'],
                'assigned_area' => $validated['assigned_area'],
                'updated_at' => now()->toDateTimeString()
            ]);

        // Fetch updated record
        $updatedEnforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', $validated['enforcer_id'])
            ->first();

        return response()->json([
            'success' => '✅ Driver details updated successfully',
            'enforcer' => $updatedEnforcer
        ]);
    }


    public function delete(Request $request)
    {
        $enforcerId = $request->did;

        // Delete from MySQL
        $deleted = DB::table('traffic_enforcers')->where('enforcer_id', $enforcerId)->delete();

        if ($deleted) {
            // Delete from Firebase
            $this->firebase->getDatabase()
                ->getReference('traffic_enforcers/' . $enforcerId)
                ->remove();

            return response()->json(['success' => 'Driver deleted successfully']);
        } else {
            return response()->json(['error' => 'Driver not found.']);
        }
    }

    public function toggleLockAll()
    {
        // Count how many enforcers are currently locked
        $lockedCount = DB::table('traffic_enforcers')->where('is_locked', 1)->count();
        $totalEnforcers = DB::table('traffic_enforcers')->count();

        // If all are locked, we will unlock; otherwise, lock all
        $newStatus = ($lockedCount === $totalEnforcers) ? 0 : 1;

        // Update all in MySQL
        DB::table('traffic_enforcers')->update(['is_locked' => $newStatus]);

        // Fetch all enforcers to update in Firebase
        $enforcers = DB::table('traffic_enforcers')->get();

        // Update each enforcer in Firebase
        foreach ($enforcers as $enforcer) {
            $this->firebase->getDatabase()
                ->getReference('traffic_enforcers/' . $enforcer->enforcer_id)
                ->update([
                    'is_locked' => (bool) $newStatus,
                    'updated_at' => now()->toDateTimeString()
                ]);
        }

        $message = $newStatus ? 'All officers locked successfully.' : 'All officers unlocked successfully.';

        return response()->json([
            'success' => true,
            'status' => $newStatus,  // Return the new status (1 for locked, 0 for unlocked)
            'message' => $message
        ]);
    }

    // Logout
    public function logout()
    {
        Session::flush();
        return redirect('/enforcer-login')->with('success', 'Logged out successfully.');
    }

    public function edit()
    {
        if (!session()->has('enforcer_id')) {
            return redirect()->route('enforcer.login')->with('error', 'Please login first.');
        }

        return view('enforcer.enforcer-profile');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldpassword' => 'required',
            'newpassword' => [
                'required',
                'min:8',
                'regex:/[a-z]/',      // at least one lowercase
                'regex:/[A-Z]/',      // at least one uppercase
                'regex:/[0-9]/'       // at least one number
            ],
            'passwordconfirm' => 'required|same:newpassword',
        ], [
            'newpassword.regex' => 'New password must contain at least one uppercase letter, one lowercase letter, and one number.',
        ]);

        // Get the enforcer
        $enforcer = DB::table('traffic_enforcers')->where('enforcer_id', session('enforcer_id'))->first();

        if (!$enforcer) {
            return redirect()->back()->with('error', 'Enforcer not found.');
        }

        if (!Hash::check($request->oldpassword, $enforcer->enforcer_password)) {
            return redirect()->back()->with('error', 'Old password is incorrect.');
        }

        // Update the password
        DB::table('traffic_enforcers')->where('enforcer_id', $enforcer->enforcer_id)->update([
            'enforcer_password' => Hash::make($request->newpassword),
        ]);

        // Optional: log out after password change
        Session::flush();
        return redirect()->route('enforcer-login')->with('success', 'Password changed successfully. Please log in again.');
    }
}
