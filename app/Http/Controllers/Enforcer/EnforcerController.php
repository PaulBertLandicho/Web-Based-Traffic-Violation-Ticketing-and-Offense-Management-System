<?php

namespace App\Http\Controllers\Enforcer;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnforcerRegisteredMail;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;
use App\Models\TrafficEnforcer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\LogHelper;


class EnforcerController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function sendNotice(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'enforcer_id' => 'nullable|string' // if null → send to all
        ]);
        LogHelper::record(session('admin_id') ?? 'ADMIN', 'SEND_NOTICE', "Notice sent to {$request->enforcer_id}");

        if ($request->enforcer_id === 'all') {
            // Save notice for all enforcers
            $enforcers = DB::table('traffic_enforcers')->pluck('enforcer_id');
            foreach ($enforcers as $id) {
                $notif = Notification::create([
                    'enforcer_id' => $request->enforcer_id,
                    'title'       => $request->title,
                    'message'     => $request->message,
                    'is_read'     => 0,
                ]);
                // Send to Firebase for realtime
                $this->firebase->getDatabase()
                    ->getReference('notifications/' . $id)
                    ->push([
                        'id'        => $notif->id,   // 👈 store MySQL ID
                        'title'     => $notif->title,
                        'message'   => $notif->message,
                        'is_read'   => false,
                        'created_at' =>  now()->timezone('Asia/Manila')->toDateTimeString() // 👈 unified field name
                    ]);
            }
        } else {
            // Send to one enforcer
            $notif = Notification::create([
                'enforcer_id' => $request->enforcer_id,
                'title'       => $request->title,
                'message'     => $request->message,
                'is_read'     => 0,
            ]);

            $this->firebase->getDatabase()
                ->getReference('notifications/' . $request->enforcer_id)
                ->push([
                    'id'        => $notif->id,   // 👈 store MySQL ID
                    'title'     => $notif->title,
                    'message'   => $notif->message,
                    'is_read'   => false,
                    'created_at' =>  now()->timezone('Asia/Manila')->toDateTimeString() // 👈 match frontend
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Notification sent successfully!']);
    }

    public function markNotificationRead(Request $request)
    {
        $request->validate(['notification_id' => 'required']);

        DB::table('notifications')
            ->where('id', $request->notification_id)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }

    public function otpPage()
    {
        if (!Session::has('pending_enforcer_id')) {
            return redirect()->route('enforcer.login')->with('error', 'Unauthorized access.');
        }

        return view('enforcer.enforcer-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $enforcerId = Session::get('pending_enforcer_id');

        $enforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', $enforcerId)
            ->first();

        if (!$enforcer) {
            return back()->with('error', 'Enforcer not found.');
        }

        // Expired?
        if (now()->greaterThan($enforcer->otp_expires_at)) {
            return back()->with('error', 'Your OTP has expired. Please login again.');
        }

        // Wrong OTP
        if ($request->otp != $enforcer->otp_code) {
            return back()->with('error', 'Invalid OTP code.');
        }

        // SUCCESS — OTP Valid
        DB::table('traffic_enforcers')
            ->where('enforcer_id', $enforcerId)
            ->update([
                'otp_code' => null,
                'otp_expires_at' => null
            ]);

        // FINAL LOGIN SESSION
        Session::put('enforcer_id', $enforcer->enforcer_id);
        Session::put('enforcer_name', $enforcer->enforcer_name);
        Session::put('enforcer_email', $enforcer->enforcer_email);

        // Role
        $role = DB::table('roles')->where('role_id', $enforcer->role_id)->value('role_name');
        Session::put('role', $role);

        Session::forget('pending_enforcer_id');

        return redirect()->route('enforcer.enforcer-dashboard')
            ->with('success', 'Login successful!');
    }

    public function resendOtp(Request $request)
    {
        if (!Session::has('pending_enforcer_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $enforcerId = Session::get('pending_enforcer_id');

        $enforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', $enforcerId)
            ->first();

        if (!$enforcer) {
            return response()->json(['success' => false, 'message' => 'Enforcer not found']);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);

        DB::table('traffic_enforcers')
            ->where('enforcer_id', $enforcerId)
            ->update([
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(5)
            ]);

        // Send email
        Mail::raw(
            "Your NEW OTP code is: $otp\nThis code expires in 5 minutes.",
            function ($message) use ($enforcer) {
                $message->to($enforcer->enforcer_email)
                    ->subject('Your New OTP Code');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'A new OTP has been sent to your email.'
        ]);
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

            if ($enforcer->is_archived) {
                LogHelper::record($enforcer->enforcer_id, 'LOGIN_FAILED', 'Account archived.');
                return back()->with('error', 'Your account has been archived.');
            }

            if ($enforcer->is_locked) {
                LogHelper::record($enforcer->enforcer_id, 'LOGIN_FAILED', 'Account locked.');
                return back()->with('error', 'Your account is locked. Contact admin.');
            }

            // Generate OTP
            $otp = rand(100000, 999999);
            DB::table('traffic_enforcers')
                ->where('enforcer_id', $enforcer->enforcer_id)
                ->update([
                    'otp_code' => $otp,
                    'otp_expires_at' => now()->addMinutes(5)
                ]);

            // Send email OTP
            Mail::raw(
                "Your OTP code is: $otp\nThis code will expire in 5 minutes.",
                function ($message) use ($enforcer) {
                    $message->to($enforcer->enforcer_email)
                        ->subject('Your Login OTP Code');
                }
            );

            // Store pending session
            Session::put('pending_enforcer_id', $enforcer->enforcer_id);

            // 🔵 LOGGING HERE
            LogHelper::record($enforcer->enforcer_id, 'LOGIN_REQUEST', 'Password verified. OTP sent.');

            return redirect()->route('enforcer.otp-page')
                ->with('success', 'OTP has been sent to your email.');
        }

        // ❌ WRONG PASSWORD LOG
        LogHelper::record($request->enforcer_id, 'LOGIN_FAILED', 'Incorrect ID or password.');

        return back()->with('error', 'Invalid ID or Password!');
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

        $enforcerId = session('enforcer_id');

        $enforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', session('enforcer_id'))
            ->first();

        if ($enforcer && $enforcer->is_locked) {
            session()->flush();
            return redirect()->route('enforcer.login')->with('error', 'Your account is locked.');
        }

        // Count pending violations
        $pendingViolationsCount = DB::table('enforcer_violations')
            ->where('enforcer_id', $enforcerId)
            ->where('status', 'pending')
            ->count();

        // Check if first violation exists (complaint_count = 1)
        $firstViolation = DB::table('enforcer_violations')
            ->where('enforcer_id', $enforcerId)
            ->where('status', 'pending')
            ->where('complaint_count', 1)
            ->first();

        $firstViolationWarning = $firstViolation ?
            'This is your first violation filed against you in your role as an enforcer. Your account will remain unlocked.'
            : null;


        // ✅ Auto logout if archived
        if ($enforcer->is_archived) {
            session()->flush();
            return redirect()->route('enforcer.login')->with('error', 'Your account has been archived. Please contact the administrator.');
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
            'pendingViolationsCount' => $pendingViolationsCount,
            'firstViolationWarning' => $firstViolationWarning,
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

    public function checkStatus()
    {
        if (!session()->has('enforcer_id')) {
            return response()->json(['status' => 'logged_out']);
        }

        $enforcer = DB::table('traffic_enforcers')
            ->where('enforcer_id', session('enforcer_id'))
            ->first();

        if (!$enforcer) {
            return response()->json(['status' => 'not_found']);
        }

        if ($enforcer->is_archived) {
            session()->flush();
            return response()->json(['status' => 'archived']);
        }

        if ($enforcer->is_locked) {
            session()->flush();
            return response()->json(['status' => 'locked']);
        }

        return response()->json(['status' => 'active']);
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
            'contactno' => [
                'required',
                'regex:/^9\d{9}$/', // must start with 9 and have 10 digits
            ],
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
            LogHelper::record($id, $newStatus ? 'ACCOUNT_LOCKED' : 'ACCOUNT_UNLOCKED');

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
            ->leftJoin('vehicles as v', 'f.vehicle_no', '=', 'v.vehicle_no')
            ->select(
                'd.license_id',
                'd.driver_name',
                'f.ref_no as offense_number',
                'f.violation_type',
                'f.total_amount as penalty_applied',
                'f.vehicle_no',
                'v.vehicle_type',
                'f.place',
                'f.status',
                'f.created_at'
            )
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

        LogHelper::record($validated['enforcer_id'], 'UPDATE_PROFILE', 'Enforcer profile updated');

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
        if (session()->has('enforcer_id')) {
            LogHelper::record(session('enforcer_id'), 'LOGOUT', 'User logged out.');
        }

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


    public function updateProfile(Request $request)
    {
        $request->validate([
            'enforcer_name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $enforcerId = session('enforcer_id');
        $enforcer = DB::table('traffic_enforcers')->where('enforcer_id', $enforcerId)->first();

        if (!$enforcer) {
            return redirect()->back()->with('error', 'Enforcer not found.');
        }

        $changes = []; // collect what was updated

        // -----------------------------
        // ✅ Update Signature
        // -----------------------------
        if ($request->hasFile('enforcer_signature')) {
            $file = $request->file('enforcer_signature');
            $filename = $enforcerId . '_signature_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/uploads/enforcer_signatures'), $filename);

            $signaturePath = 'assets/uploads/enforcer_signatures/' . $filename;

            DB::table('traffic_enforcers')->where('enforcer_id', $enforcerId)->update([
                'enforcer_signature' => $signaturePath
            ]);

            Session::put('enforcer_signature', $signaturePath);

            $changes[] = "Updated Signature";
        }

        // -----------------------------
        // ✅ Update Profile Image
        // -----------------------------
        $imagePath = $enforcer->profile_image;

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = $enforcerId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/uploads/enforcer_profiles'), $filename);

            $imagePath = 'assets/uploads/enforcer_profiles/' . $filename;
            $changes[] = "Updated Profile Image";
        }

        // -----------------------------
        // ✅ Update Name
        // -----------------------------
        if ($request->enforcer_name !== $enforcer->enforcer_name) {
            $changes[] = "Changed Name from '{$enforcer->enforcer_name}' to '{$request->enforcer_name}'";
        }

        // -----------------------------
        // ✅ Save Profile Updates
        // -----------------------------
        DB::table('traffic_enforcers')->where('enforcer_id', $enforcerId)->update([
            'enforcer_name' => $request->enforcer_name,
            'profile_image' => $imagePath,
            'updated_at' => now(),
        ]);

        // Update session
        Session::put('enforcer_name', $request->enforcer_name);
        Session::put('enforcer_profile_image', $imagePath);

        // -----------------------------
        // ✅ Firebase Sync
        // -----------------------------
        $this->firebase->getDatabase()
            ->getReference('traffic_enforcers/' . $enforcerId)
            ->update([
                'enforcer_name' => $request->enforcer_name,
                'profile_image' => $imagePath,
                'updated_at' => now()->toDateTimeString()
            ]);

        // -----------------------------
        // ✅ USER LOG ACTIVITY (IMPORTANT)
        // -----------------------------
        DB::table('user_logs')->insert([
            'enforcer_id' => $enforcerId,
            'action' => 'Update Profile',
            'details' => implode(", ", $changes), // summary of changes
            'ip_address' => $request->ip(),
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // -----------------------------
        // OPTIONAL: Firebase Log (for notifications)
        // -----------------------------
        $this->firebase->getDatabase()->getReference("admin_user_logs")->push([
            'title' => "Enforcer Updated Profile",
            'message' => "{$request->enforcer_name} updated their profile.",
            'status' => "unread",
            'created_at' => now()->toDateTimeString()
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function issueViolation(Request $request)
    {
        $enforcerId = $request->enforcer_id;

        // Count existing pending violations
        $pendingCount = DB::table('enforcer_violations')
            ->where('enforcer_id', $enforcerId)
            ->where('status', 'pending')
            ->count();

        // Insert the new violation
        $violationId = DB::table('enforcer_violations')->insertGetId([
            'enforcer_id'    => $enforcerId,
            'violation_type' => $request->violation_type,
            'details'        => $request->details,
            'penalty_amount' => $request->penalty_amount,
            'created_at'     => now(),
            'updated_at'     => now(),
            'complaint_count' => $pendingCount + 1, // Track number of complaints
        ]);

        // Check if this is the first violation
        if ($pendingCount == 0) {
            // Store warning in session for automatic dashboard popup
            session()->flash('first_violation_warning', 'This is the first violation filed against this enforcer. Account will remain unlocked.');

            return response()->json([
                'warning' => session('first_violation_warning'),
                'violation_id' => $violationId,
                'enforcer_id' => $enforcerId
            ]);
        } else {
            // Second or subsequent violation: lock the account
            DB::table('traffic_enforcers')
                ->where('enforcer_id', $enforcerId)
                ->update(['is_locked' => 1]);

            return response()->json([
                'success' => 'Violation issued. Enforcer account has been locked due to multiple violations.',
                'violation_id' => $violationId,
                'enforcer_id' => $enforcerId
            ]);
        }
    }


    public function settleViolation(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:enforcer_violations,id',
            'remarks' => 'nullable|string|max:500'
        ]);

        $violationId = $request->id;

        // ✅ Mark violation as settled
        DB::table('enforcer_violations')
            ->where('id', $violationId)
            ->update([
                'status' => 'settled',
                'remarks' => $request->remarks,
                'settled_at' => now()
            ]);

        // ✅ Get updated violation
        $violation = DB::table('enforcer_violations')->where('id', $violationId)->first();

        // ✅ Get enforcer ID of this violation
        $enforcerId = $violation->enforcer_id;

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

        // ✅ Return the updated violation for frontend
        return response()->json([
            'success' => true,
            'violation' => $violation, // <--- this is used to append to history table
            'enforcer_id' => $enforcerId,
            'unlocked' => $unlocked
        ]);
    }
}
