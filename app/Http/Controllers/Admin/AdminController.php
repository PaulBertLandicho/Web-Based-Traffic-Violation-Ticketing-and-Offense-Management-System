<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\IssuedFineTicket;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Show login form
    public function admin()
    {
        return view('admin.admin-login');
    }

    // Handle login logic
    public function login(Request $request)
    {
        $request->validate([
            'admin_email' => 'required|email',
            'admin_password' => 'required'
        ]);

        $admin = DB::table('traffic_admins')
            ->where('admin_email', $request->admin_email)
            ->first();

        if ($admin && Hash::check($request->admin_password, $admin->admin_password)) {
            Session::put('admin_email', $admin->admin_email);
            Session::put('admin_name', $admin->admin_name);
            Session::put('admin_id', $admin->admin_id);

            // Handle role gracefully
            $role = DB::table('roles')->where('role_id', $admin->role_id)->value('role_name') ?? 'admin';
            Session::put('role', $role);

            return redirect()->route('admin.admin-dashboard');
        }

        return back()->with('error', 'Incorrect Email or Password!');
    }

    // Admin dashboard
    public function adminDashboard()
    {
        // This is block to avoid auto login 
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        // === Dashboard Data ===
        $pendingFineAmount = DB::table('issued_fine_tickets')->where('status', 'Pending')->sum('total_amount');
        $paidFineAmount = DB::table('issued_fine_tickets')->where('status', 'Paid')->sum('total_amount');

        $totalPending = $pendingFineAmount;
        $totalPaid = $paidFineAmount;


        $totalFineAmount = DB::table('issued_fine_tickets')->sum('total_amount');

        $issuedDriversCount = DB::table('driver_list')->distinct('license_id')->count('license_id');

        $enforcersCount = DB::table('traffic_enforcers')->count();
        $provisionsCount = DB::table('traffic_violations')->count();

        // Get the full data
        $vehicleTypesData = DB::table('vehicles')
            ->select('vehicle_type', DB::raw('COUNT(*) as count'))
            ->groupBy('vehicle_type')
            ->orderByDesc('count')
            ->get();

        // Correct label and data arrays
        $vehicleTypes = $vehicleTypesData->pluck('vehicle_type'); // array of strings
        $vehicleCounts = $vehicleTypesData->pluck('count');       // array of numbers


        $rawViolations = DB::table('issued_fine_tickets')->pluck('violation_type');
        $violationCounts = [];

        foreach ($rawViolations as $violationEntry) {
            foreach (explode(',', $violationEntry) as $type) {
                $cleaned = strtolower(trim(preg_replace('/^\d+\s*-\s*/', '', $type)));
                if (!empty($cleaned)) {
                    $violationCounts[$cleaned] = ($violationCounts[$cleaned] ?? 0) + 1;
                }
            }
        }

        arsort($violationCounts);
        $violationTypeData = collect(array_slice($violationCounts, 0, 6));
        $violationTypes = $violationTypeData->keys();
        $violationCounts = $violationTypeData->values();
        $pendingFineAmountCount = IssuedFineTicket::where('status', 'pending')->count();

        $issuedFineStats = DB::table('issued_fine_tickets')
            ->select(DB::raw("MONTH(created_at) as month"), DB::raw('COUNT(*) as count'))
            ->groupBy('month')->orderBy('month')->get();

        $issuedFineMonths = $issuedFineStats->pluck('month')->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));
        $issuedFineCounts = $issuedFineStats->pluck('count');

        $fineStats = DB::table('issued_fine_tickets')
            ->select(DB::raw("MONTH(created_at) as month"), DB::raw('SUM(total_amount) as total'))
            ->groupBy('month')->orderBy('month')->get();

        $totalFineMonths = $fineStats->pluck('month')->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));

        $monthlyTotals = [
            'janTotal' => 0,
            'febTotal' => 0,
            'marchTotal' => 0,
            'aprilTotal' => 0,
            'mayTotal' => 0,
            'juneTotal' => 0,
            'julyTotal' => 0,
            'augustTotal' => 0,
            'sepTotal' => 0,
            'octTotal' => 0,
            'novTotal' => 0,
            'decTotal' => 0,
        ];

        foreach ($fineStats as $stat) {
            $monthNum = (int) $stat->month;
            $total    = (float) $stat->total;

            $monthName = strtolower(date('M', mktime(0, 0, 0, $monthNum, 1))); // jan, feb, ...
            $monthlyTotals["{$monthName}Total"] = $total;
        }

        // Extract individual month variables like $janTotal, $febTotal, etc.
        extract($monthlyTotals);


        $barangayViolations = DB::table('issued_fine_tickets')
            ->select('place', DB::raw('COUNT(*) as count'))
            ->groupBy('place')
            ->pluck('count', 'place')
            ->toArray();

        // Define monthly total fine amount per month
        $monthlyTotals = [];

        for ($month = 1; $month <= 12; $month++) {
            $total = DB::table('issued_fine_tickets')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount');

            $monthlyTotals[$month] = $total;
        }
        return view('admin.admin-dashboard', [
            'pendingFineAmount' => $pendingFineAmount,
            'pendingFineAmountCount' => $pendingFineAmountCount,
            'paidFineAmount' => $paidFineAmount,
            'totalFineAmount' => $totalFineAmount,
            'issuedDriversCount' => $issuedDriversCount,
            'enforcersCount' => $enforcersCount,
            'provisionsCount' => $provisionsCount,
            'vehicleTypesData' => $vehicleTypesData,
            'vehicleCounts' => $vehicleCounts,
            'vehicleTypes' => $vehicleTypes,
            'violationTypeData' => $violationTypeData,
            'issuedFineMonths' => $issuedFineMonths,
            'issuedFineCounts' => $issuedFineCounts,
            'totalFineMonths' => $totalFineMonths,
            'barangayViolations' => $barangayViolations,
            'violationTypes' => $violationTypes,
            'violationCounts' => $violationCounts,
            'totalPending' => $totalPending,
            'totalPaid' => $totalPaid,
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
            'decTotal' => $monthlyTotals[12]
        ]);
    }

    // inside AdminController.php
    public function fetchSummary()
    {
        $pendingFineAmount = DB::table('issued_fine_tickets')->where('status', 'Pending')->sum('total_amount');
        $paidFineAmount = DB::table('issued_fine_tickets')->where('status', 'Paid')->sum('total_amount');
        $totalFineAmount = DB::table('issued_fine_tickets')->sum('total_amount');
        $issuedDriversCount = DB::table('driver_list')->distinct('license_id')->count('license_id');
        $enforcersCount = DB::table('traffic_enforcers')->count();
        $provisionsCount = DB::table('traffic_violations')->count();

        return response()->json([
            'pendingFineAmount' => $pendingFineAmount,
            'paidFineAmount' => $paidFineAmount,
            'totalFineAmount' => $totalFineAmount,
            'issuedDriversCount' => $issuedDriversCount,
            'enforcersCount' => $enforcersCount,
            'provisionsCount' => $provisionsCount,
        ]);
    }


    // // Show form to add new admin
    // public function create()
    // {
    //     return view('admin.add-admin');
    // }

    // // Create new Admin
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'admin_email' => 'required|email|unique:traffic_admins,admin_email',
    //         'admin_password' => 'required|min:6',
    //         'admin_name' => 'required|string|max:255',
    //     ]);

    //     $adminRoleId = DB::table('roles')->where('role_name', 'traffic admin')->value('role_id');

    //     if (!$adminRoleId) {
    //         return back()->with('error', 'Admin role not found. Please check roles table.');
    //     }

    //     DB::table('traffic_admins')->insert([
    //         'admin_email'    => $request->admin_email,
    //         'admin_password' => Hash::make($request->admin_password),
    //         'admin_name'     => $request->admin_name,
    //         'code'           => random_int(100000, 999999),
    //         'status'         => 'verify',
    //         'role_id'        => $adminRoleId,
    //     ]);

    //     return redirect()->route('admin.admin-dashboard')->with('success', 'Admin added successfully.');
    // }

    // Logout
    public function logout()
    {
        Session::flush();
        return redirect('/admin-login')->with('success', 'Logged out successfully.');
    }

    public function fetchIssuedFines(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year', date('Y'));

        $labels = [];
        $values = [];

        if ($month) {
            $count = DB::table('issued_fine_tickets')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();

            $labels[] = date('F', mktime(0, 0, 0, $month, 1));
            $values[] = $count;
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = date('F', mktime(0, 0, 0, $m, 1));
                $count = DB::table('issued_fine_tickets')
                    ->whereMonth('created_at', $m)
                    ->whereYear('created_at', $year)
                    ->count();
                $values[] = $count;
            }
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }
    public function totalIssuedAmount(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year', date('Y'));

        $labels = [];
        $values = [];

        if ($month) {
            $total = DB::table('issued_fine_tickets')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('total_amount');

            $labels[] = date('F', mktime(0, 0, 0, $month, 1));
            $values[] = $total;
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = date('F', mktime(0, 0, 0, $m, 1));
                $total = DB::table('issued_fine_tickets')
                    ->whereMonth('created_at', $m)
                    ->whereYear('created_at', $year)
                    ->sum('total_amount');
                $values[] = $total;
            }
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function fetchBarangayViolations(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year', date('Y'));

        $query = DB::table('issued_fine_tickets')
            ->select('place', DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', $year);

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        $results = $query->groupBy('place')->orderByDesc('count')->get();

        $labels = $results->pluck('place');
        $data = $results->pluck('count');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    // Show the admin profile page with change password form
    public function editProfile()
    {
        if (!session()->has('admin_id')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        return view('admin.admin-profile');
    }

    // Update the admin's password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldpassword' => 'required',
            'newpassword' => [
                'required',
                'min:8',
                'regex:/[a-z]/',      // lowercase
                'regex:/[A-Z]/',      // uppercase
                'regex:/[0-9]/',      // number
            ],
            'passwordconfirm' => 'required|same:newpassword',
        ], [
            'newpassword.regex' => 'New password must contain upper/lowercase letters and a number.',
        ]);

        $admin = DB::table('traffic_admins')->where('admin_id', session('admin_id'))->first();

        if (!$admin) {
            return back()->with('error', 'Admin not found.');
        }

        if (!Hash::check($request->oldpassword, $admin->admin_password)) {
            return back()->with('error', 'Old password is incorrect.');
        }

        DB::table('traffic_admins')->where('admin_id', $admin->admin_id)->update([
            'admin_password' => Hash::make($request->newpassword),
        ]);

        Session::flush();
        return redirect('/admin-login')->with('success', 'Password changed. Please log in again.');
    }
}
