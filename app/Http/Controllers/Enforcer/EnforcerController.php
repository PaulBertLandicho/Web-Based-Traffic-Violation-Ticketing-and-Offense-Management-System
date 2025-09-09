<?php

namespace App\Http\Controllers\Enforcer;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;   // ✅ correct import
use App\Mail\EnforcerRegisteredMail;   // ✅ import your mailable
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;
use App\Models\TrafficEnforcer;
use Illuminate\Http\Request;

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
            // If the account is locked, show reason
            if ($enforcer->is_locked) {
                // Get latest violation complaint filed against this enforcer
                $latestViolation = DB::table('enforcer_violations')
                    ->where('enforcer_id', $enforcer->enforcer_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $reason = $latestViolation
                    ? "Your account has been locked because of a violation filed: \"{$latestViolation->violation_type}\" - {$latestViolation->details}"
                    : "Your account has been locked due to a complaint filed by a violator.";

                Session::flush();
                return redirect()->route('enforcer.login')
                    ->with('error', $reason);
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
                ->where('enforcer_id', session('enforcer_id'))
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->count('ref_no');

            $monthlyCounts[$month] = $count;
        }

        // Define monthly total fine amount per month
        $monthlyTotals = [];

        for ($month = 1; $month <= 12; $month++) {
            $total = DB::table('issued_fine_tickets')
                ->where('enforcer_id', session('enforcer_id'))
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
            'enforceremail' => 'required|email|unique:traffic_enforcers,enforcer_email',
            'enforcerpassword' => 'required|min:6',
            'enforcerpasswordconfirm' => 'required|same:enforcerpassword',
            'enforcername' => 'required',
            'assignedarea' => 'required',
            'contactno' => 'required|string',
            'gender' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        //  Format contact number
        $contact = $request->contactno;
        if (!str_starts_with($contact, '+63')) {
            $contact = '+63' . ltrim($contact, '0');
        }
        // Generate next Enforcer ID
        $lastEnforcer = DB::table('traffic_enforcers')
            ->orderBy('enforcer_id', 'desc')
            ->first();

        $nextNumber = $lastEnforcer
            ? intval(substr($lastEnforcer->enforcer_id, 3)) + 1
            : 1;

        $enforcerId = 'TE-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        // Get role_id
        $roleId = DB::table('roles')->where('role_name', 'traffic enforcer')->value('role_id');

        $data = [
            'enforcer_id' => $enforcerId,
            'enforcer_email' => $request->enforceremail,
            'enforcer_password' => Hash::make($request->enforcerpassword),
            'enforcer_name' => $request->enforcername,
            'assigned_area' => $request->assignedarea,
            'contact_no' => $contact,
            'gender' => $request->gender,
            'registered_at' => now()->toDateString(),
            'code' => random_int(100000, 999999),
            'is_locked' => false,
            'role_id' => $roleId,
        ];

        DB::table('traffic_enforcers')->insert($data);

        // Send Email Notification
        Mail::to($request->enforceremail)->send(new EnforcerRegisteredMail($data));

        return redirect()->route('enforcers.create')
            ->with('success', "Traffic Enforcer {$enforcerId} added successfully and notified via email!");
    }

    public function index()
    {
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        // Get all enforcers
        $enforcers = DB::table('traffic_enforcers')
            ->where('is_archived', 0)
            ->get();

        $archivedEnforcers = DB::table('traffic_enforcers')
            ->where('is_archived', 1)
            ->get();

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
            'archivedEnforcers',
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
        $id = $request->id;

        $enforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', $id)
            ->first();

        if (!$enforcer) {
            return response()->json(['error' => 'Enforcer details not found.'], 404);
        }

        $violations = DB::table('enforcer_violations')
            ->where('enforcer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $drivers = DB::table('issued_fine_tickets as f')
            ->join('driver_list as d', 'f.license_id', '=', 'd.license_id')
            ->select('d.license_id', 'd.driver_name', 'f.ref_no', 'f.violation_type', 'f.total_amount', 'f.created_at')
            ->where('f.enforcer_id', $id)
            ->orderBy('f.created_at', 'desc')
            ->get();

        // This function should only return details, not update status

        return response()->json([
            'enforcer'   => $enforcer,
            'violations' => $violations,
            'drivers'    => $drivers
        ]);
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
            'success' => ' Driver details updated successfully',
            'enforcer' => $updatedEnforcer
        ]);
    }


    // 🟢 Archive Enforcer Instead of Delete
    public function archive(Request $request)
    {
        $enforcerId = $request->aid;

        $enforcer = DB::table('traffic_enforcers')->where('enforcer_id', $enforcerId)->first();

        if (!$enforcer) {
            return response()->json(['error' => 'Enforcer not found.']);
        }

        // Update archive status in DB
        DB::table('traffic_enforcers')
            ->where('enforcer_id', $enforcerId)
            ->update(['is_archived' => 1]);

        // Update in Firebase too
        $this->firebase->getDatabase()
            ->getReference('traffic_enforcers/' . $enforcerId)
            ->update(['is_archived' => true]);

        return response()->json(['success' => 'Enforcer archived successfully']);
    }

    public function archived()
    {
        // Fetch enforcers where is_archived = 1 (archived)
        $archivedEnforcers = DB::table('traffic_enforcers')
            ->where('is_archived', 1)
            ->get();

        return response()->json(['enforcers' => $archivedEnforcers]);
    }

    public function restore(Request $request)
    {
        $enforcerId = $request->rid;

        $enforcer = DB::table('traffic_enforcers')->where('enforcer_id', $enforcerId)->first();

        if (!$enforcer) {
            return response()->json(['error' => 'Enforcer not found.']); // ✅ fixed wording
        }

        DB::table('traffic_enforcers')
            ->where('enforcer_id', $enforcerId)
            ->update(['is_archived' => 0]);

        return response()->json([
            'success' => 'Enforcer restored successfully!',
            'enforcer' => [
                'enforcer_id'   => $enforcer->enforcer_id,
                'enforcer_name' => $enforcer->enforcer_name,
                'assigned_area' => $enforcer->assigned_area,
                'gender'        => $enforcer->gender,
            ]
        ]);
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
            'status' => $newStatus,
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

    public function issueViolation(Request $request)
    {
        $enforcerId = $request->enforcer_id;

        DB::table('enforcer_violations')->insert([
            'enforcer_id'   => $enforcerId,
            'violation_type' => $request->violation_type,
            'details'       => $request->details,
            'penalty_amount' => $request->penalty_amount,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // 🛑 Auto-lock enforcer immediately
        DB::table('traffic_enforcers')
            ->where('enforcer_id', $enforcerId)
            ->update(['is_locked' => 1]);

        return response()->json([
            'success' => 'Violation issued and enforcer account locked automatically.',
            'enforcer_id' => $enforcerId
        ]);
    }

    public function settleSingleViolation(Request $request)
    {
        $violationId = $request->id;

        // ✅ Mark violation as settled
        DB::table('enforcer_violations')
            ->where('id', $violationId)
            ->update([
                'status' => 'settled',
                'updated_at' => now()
            ]);
        // ✅ Get enforcer ID of this violation
        $enforcerId = DB::table('enforcer_violations')
            ->where('id', $violationId)
            ->value('enforcer_id');

        // ✅ Count remaining pending violations
        $pending = DB::table('enforcer_violations')
            ->where('enforcer_id', $enforcerId)
            ->where('status', 'pending')
            ->count();

        $unlocked = false;

        if ($pending === 0) {
            // 🔓 Unlock enforcer if no pending violations
            DB::table('traffic_enforcers')
                ->where('enforcer_id', $enforcerId)
                ->update(['is_locked' => 0]);

            // 🔓 Update Firebase
            $this->firebase->getDatabase()
                ->getReference('traffic_enforcers/' . $enforcerId)
                ->update([
                    'is_locked' => false,
                    'updated_at' => now()->toDateTimeString()
                ]);

            $unlocked = true;
        }

        return response()->json([
            'success' => true,
            'enforcer_id' => $enforcerId,
            'unlocked' => $unlocked
        ]);
    }
}
