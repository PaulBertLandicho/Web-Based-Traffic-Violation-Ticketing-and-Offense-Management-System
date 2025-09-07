<?php

namespace App\Http\Controllers\Driver;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function create()
    {
        return view('enforcer.add_driver');
    }

    public function store(Request $request, FirebaseService $firebaseService)
    {
        // 🔍 Validate input
        $validator = Validator::make($request->all(), [
            'licenseid' => [
                'required',
                'regex:/^([A-Z]{1}[0-9]{2}-[0-9]{2}-[0-9]{6}|[A-Z]{2}-[0-9]{2}-[0-9]{6})$/'
            ],
            'drivername' => 'required|string',
            'licensetype' => 'required',
            'homeaddress' => 'required|string',
            'contactno' => 'required|string',
            'licenseissuedate' => 'required|date',
            'licenseexpiredate' => 'required|date|after:licenseissuedate',
            'dateofbirth' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //  Format contact number
        $contact = $request->contactno;
        if (!str_starts_with($contact, '+63')) {
            $contact = '+63' . ltrim($contact, '0');
        }

        //  Save to MySQL
        DB::table('driver_list')->insert([
            'license_id' => $request->licenseid,
            'driver_name' => $request->drivername,
            'home_address' => $request->homeaddress,
            'contact_no' => $contact,
            'license_issue_date' => $request->licenseissuedate,
            'license_expire_date' => $request->licenseexpiredate,
            'date_of_birth' => $request->dateofbirth,
            'license_type' => $request->licensetype,
            'registered_at' => now()->toDateString(),
            'status' => 'verified'
        ]);

        //  Use correct variable for Firebase ID
        $licenseId = $request->licenseid;
        // Sync to Firebase Realtime Database
        try {
            $firebase = $firebaseService->getDatabase();
            $firebase->getReference('driver_list/' . $request->licenseid)->set([
                'license_id' => $request->licenseid,
                'driver_name' => $request->drivername,
                'home_address' => $request->homeaddress,
                'contact_no' => $contact,
                'license_issue_date' => $request->licenseissuedate,
                'license_expire_date' => $request->licenseexpiredate,
                'date_of_birth' => $request->dateofbirth,
                'license_type' => $request->licensetype,
                'registered_at' => now()->toDateTimeString(),
                'status' => 'verified'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['firebase' => '❌ Failed to sync with Firebase: ' . $e->getMessage()]);
        }

        return redirect()->route('fine.create', ['license_id' => $request->licenseid])
            ->with('success', 'Driver added successfully! Now you can issue a fine.');
    }

    public function view()
    {
        // This is block to avoid auto login 
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }
        // Active drivers
        $drivers = DB::table('driver_list')
            ->where('is_archived', 0)
            ->get();

        // Archived drivers
        $archivedDrivers = DB::table('driver_list')
            ->where('is_archived', 1)
            ->get();

        return view('admin.view_all_drivers',  compact('drivers', 'archivedDrivers'));
    }

    public function getDriverDetails(Request $request)
    {
        $driver = DB::table('driver_list')
            ->leftJoin('issued_fine_tickets', 'driver_list.license_id', '=', 'issued_fine_tickets.license_id')
            ->leftJoin('vehicles', 'issued_fine_tickets.vehicle_no', '=', 'vehicles.vehicle_no')
            ->where('driver_list.license_id', $request->did)
            ->select('driver_list.*', 'vehicles.vehicle_type')
            ->first();

        $violations = DB::table('issued_fine_tickets')
            ->where('license_id', $request->did)
            ->orderByDesc('issued_date')
            ->get();

        return response()->json([
            'driver' => $driver,
            'violations' => $violations
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'license_id' => 'required',
            'driver_name' => 'required|string',
            'home_address' => 'required|string',
            'license_issue_date' => 'required|date',
            'license_expire_date' => 'required|date|after_or_equal:license_issue_date',
        ]);

        DB::table('driver_list')->where('license_id', $validated['license_id'])->update([
            'driver_name' => $validated['driver_name'],
            'home_address' => $validated['home_address'],
            'license_issue_date' => $validated['license_issue_date'],
            'license_expire_date' => $validated['license_expire_date']
        ]);

        // Update in Firebase
        $this->firebase->getDatabase()
            ->getReference('driver_list/' . $validated['license_id'])
            ->update([
                'driver_name' => $validated['driver_name'],
                'home_address' => $validated['home_address'],
                'license_issue_date' => $validated['license_issue_date'],
                'license_expire_date' => $validated['license_expire_date']
            ]);

        return response()->json(['success' => '✅ Driver details updated successfully']);
    }

    public function archive(Request $request)
    {
        $licenseId = $request->input('did');

        // 🔍 Check if driver has PENDING violations
        $hasPending = DB::table('issued_fine_tickets')
            ->where('license_id', $licenseId)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'error' => '❌ Cannot archive driver: they still have pending violations.'
            ], 400);
        }

        // 🔍 Check if driver has at least 1 paid violation
        $hasPaid = DB::table('issued_fine_tickets')
            ->where('license_id', $licenseId)
            ->where('status', 'paid')
            ->exists();

        if ($hasPaid) {
            // ✅ Archive instead of delete
            DB::table('driver_list')
                ->where('license_id', $licenseId)
                ->update(['is_archived' => 1]);

            $this->firebase->getDatabase()
                ->getReference('driver_list/' . $licenseId)
                ->update(['is_archived' => true]);

            return response()->json(['success' => '🗄️ Driver archived successfully.']);
        }

        // 🚫 No fines at all → safe to delete
        $deleted = DB::table('driver_list')->where('license_id', $licenseId)->delete();

        if ($deleted) {
            $this->firebase->getDatabase()
                ->getReference('driver_list/' . $licenseId)
                ->remove();

            return response()->json(['success' => '🗑️ Driver deleted successfully.']);
        }

        return response()->json(['error' => '❌ Driver not found.']);
    }

    public function archived()
    {
        $archivedEnforcers = DB::table('driver_list')
            ->where('is_archived', 1)
            ->get();

        return response()->json(['drivers' => $archivedEnforcers]);
    }


    public function restore($id)
    {
        $driver = DB::table('driver_list')->where('license_id', $id)->first();

        if (!$driver) {
            return redirect()->back()->with('error', 'Driver not found.');
        }

        DB::table('driver_list')
            ->where('license_id', $id)
            ->update(['is_archived' => 0]);

        return redirect()->back()->with('success', 'Driver restored successfully.');
    }

    public function downloadPDF($refNo)
    {
        $ticket = DB::table('issued_fine_tickets')
            ->join('driver_list', 'issued_fine_tickets.license_id', '=', 'driver_list.license_id')
            ->leftJoin('vehicles', 'issued_fine_tickets.vehicle_no', '=', 'vehicles.vehicle_no')
            ->where('issued_fine_tickets.ref_no', $refNo)
            ->select(
                'issued_fine_tickets.*',
                'driver_list.driver_name',
                'vehicles.vehicle_type'
            )
            ->firstOrFail();

        $pdf = Pdf::loadView('driver.ticket-pdf', compact('ticket'))->setPaper('A4');
        return $pdf->download('Violation-Ticket-' . $ticket->ref_no . '.pdf');
    }
}
