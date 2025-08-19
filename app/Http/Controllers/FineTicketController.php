<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\FirebaseService;
use Illuminate\Support\Str;

class FineTicketController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function create($license_id)
    {

        $driver = DB::table('driver_list')->where('license_id', $license_id)->first();

        if (!$driver) {
            return redirect()->back();
        }

        // Get list of fine_tickets violation_type
        $violation_type = DB::table('traffic_violations')->get();

        // Get officer info from session (simulate $_SESSION[])
        $officer = [
            'enforcer_id' => Session::get('enforcer_id'),
            'enforcer_name' => Session::get('enforcer_name'),
            'assigned_area' => Session::get('assigned_area'),
        ];

        return view('enforcer.add_new_fine', compact('driver', 'violation_type', 'officer'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_id' => 'required|exists:driver_list,license_id',
            'vehicle_no' => 'required|string',
            'vehicle_make' => 'required|string',
            'vehicle_model' => 'required|string',
            'vehicle_color' => 'required|string',
            'vehicle_type' => 'required|string',
            'place' => 'required|string',
            'issued_date' => 'required|date',
            'issued_time' => 'required|string',
            'expire_date' => 'required|date|after_or_equal:issued_date',
            'violations_type' => 'required|string',
            'total_amount' => 'required|numeric|min:0'
        ]);

        // ✅ Count previous pending offenses for this driver
        // ✅ Count only pending offenses for this driver
        $previousOffenses = DB::table('issued_fine_tickets')
            ->where('license_id', $request->license_id)
            ->where('status', 'pending')
            ->count();

        $offenseNumber = $previousOffenses + 1; // Current offense count

        // ✅ Apply offense-based penalty
        $amount = $request->total_amount;
        switch ($offenseNumber) {
            case 2:
                $amount += 200; // extra for 2nd offense
                break;
            case 3:
                $amount += 500; // extra for 3rd offense
                break;
            default:
                if ($offenseNumber > 3) {
                    $amount += 1000; // extra for 4th+
                }
                break;
        }

        // ✅ Store vehicle if not exists
        $existingVehicle = DB::table('vehicles')->where('vehicle_no', $request->vehicle_no)->first();
        if (!$existingVehicle) {
            DB::table('vehicles')->insert([
                'vehicle_no' => $request->vehicle_no,
                'vehicle_make' => $request->vehicle_make,
                'vehicle_model' => $request->vehicle_model,
                'vehicle_color' => $request->vehicle_color,
                'vehicle_type' => $request->vehicle_type
            ]);
        }
        $secureToken = Str::random(32);

        // ✅ Insert fine record
        $ref_no = DB::table('issued_fine_tickets')->insertGetId([
            'enforcer_id' => Session::get('enforcer_id'),
            'license_id' => $request->license_id,
            'vehicle_no' => $request->vehicle_no,
            'place' => $request->place,
            'issued_date' => $request->issued_date,
            'issued_time' => $request->issued_time,
            'expire_date' => $request->expire_date,
            'violation_type' => $request->violations_type,
            'total_amount'    => $amount,
            'offense_number'  => $offenseNumber,
            'status' => 'pending',
            'paid_date' => now(),
            'secure_token' => $secureToken,
            'penalty_applied' => false,
            'created_at' => now()
        ]);

        // ✅ Store to Firebase
        $this->firebase->getDatabase()
            ->getReference('issued_fine_tickets/' . $ref_no)
            ->set([
                'ref_no' => $ref_no,
                'enforcer_id' => Session::get('enforcer_id'),
                'license_id' => $request->license_id,
                'vehicle_no' => $request->vehicle_no,
                'vehicle_type' => $request->vehicle_type,
                'place' => $request->place,
                'issued_date' => $request->issued_date,
                'issued_time' => $request->issued_time,
                'expire_date' => $request->expire_date,
                'violation_type' => $request->violations_type,
                'total_amount' => $request->total_amount,
                'status' => 'pending',
                'paid_date' => now()->toDateTimeString(),
            ]);

        // ✅ Persist ticket modal state in session (NOT flash)
        session([
            'show_ticket' => true,
            'ref_no' => $ref_no,
            'license_type' => $request->license_type,
            'home_address' => $request->home_address,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_no' => $request->vehicle_no,
            'vehicle_make' => $request->vehicle_make,
            'vehicle_model' => $request->vehicle_model,
            'vehicle_color' => $request->vehicle_color,
            'violation_type' => $request->violations_type,
            'place' => $request->place,
            'issued_date' => $request->issued_date,
            'issued_time' => $request->issued_time,
            'expire_date' => $request->expire_date,
            'total_amount' => $request->total_amount,
        ]);

        // ✅ Redirect without flash (so modal stays visible)
        return redirect()->route('fine.create', $request->license_id);
    }

    public function clearTicket()
    {
        session()->forget([
            'show_ticket',
            'ref_no',
            'license_type',
            'home_address',
            'vehicle_type',
            'vehicle_no',
            'vehicle_make',
            'vehicle_model',
            'vehicle_color',
            'place',
            'issued_date',
            'issued_time',
            'expire_date',
            'total_amount'
        ]);

        return redirect()->route('enforcer.enforcer-dashboard');
    }

    public function pastFines(Request $request)
    {
        $licenseId = $request->input('licenseid');

        $results = [];
        if ($licenseId) {
            $results = DB::table('issued_fine_tickets')
                ->where('license_id', $licenseId)
                ->get();
        }

        return view('enforcer.past_fines', compact('results', 'licenseId'));
    }

    public function searchPastFines(Request $request)
    {
        $licenseId = $request->input('licenseid');
        $results = [];

        if ($licenseId) {
            $results = DB::table('issued_fine_tickets')
                ->where('license_id', $licenseId)
                ->get();
        }

        // ✅ Get total fine amount for current Traffic Enforcer
        $fineAmount = DB::table('issued_fine_tickets')
            ->where('enforcer_id', Session::get('enforcer_id'))
            ->sum('total_amount');

        // ✅ Get total fine count for current Traffic Enforcer
        $fineCount = DB::table('issued_fine_tickets')
            ->where('enforcer_id', Session::get('enforcer_id'))
            ->count();

        // ✅ Get assigned area from session
        $assignedArea = Session::get('assigned_area');

        return view('enforcer.enforcer-dashboard', compact('results', 'fineAmount', 'fineCount', 'assignedArea'));
    }

    public function ajaxSearchPastFines(Request $request)
    {
        $licenseId = $request->input('licenseid');

        if (!$licenseId) {
            return response()->json(['html' => '<div class="alert alert-danger">License ID is required.</div>']);
        }

        $results = DB::table('issued_fine_tickets')
            ->where('license_id', $licenseId)
            ->get();

        $view = view('layouts.components.enforcer.partials.past_fines_table', compact('results'))->render();

        return response()->json(['html' => $view]);
    }


    public function viewReportedFines()
    {
        $enforcerId = session('enforcer_id');

        $fines = DB::table('issued_fine_tickets')
            ->where('enforcer_id', $enforcerId)
            ->orderBy('issued_date', 'desc')
            ->get();

        return view('enforcer.view_reported_fine', compact('fines'));
    }

    public function pendingTickets()
    {
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        $now = now();

        // Fetch all pending tickets
        $pendingTickets = DB::table('issued_fine_tickets')
            ->join('driver_list', 'issued_fine_tickets.license_id', '=', 'driver_list.license_id')
            ->select('issued_fine_tickets.*', 'driver_list.driver_name')
            ->where('issued_fine_tickets.status', 'pending')
            ->get();

        foreach ($pendingTickets as $ticket) {
            if ($now->gt($ticket->expire_date)) {
                // Apply penalty only if not already applied
                if (!$ticket->penalty_applied) {
                    $penalty = 100; // Or use logic: $penalty = $ticket->total_amount * 0.10;

                    DB::table('issued_fine_tickets')
                        ->where('ref_no', $ticket->ref_no)
                        ->update([
                            'total_amount' => $ticket->total_amount + $penalty,
                            'penalty_applied' => true,
                            'updated_at' => now()
                        ]);
                }
            }
        }

        // Fetch updated data again
        $pendingTickets = DB::table('issued_fine_tickets')
            ->join('driver_list', 'issued_fine_tickets.license_id', '=', 'driver_list.license_id')
            ->select('issued_fine_tickets.*', 'driver_list.driver_name')
            ->where('issued_fine_tickets.status', 'pending')
            ->get();

        return view('admin.pending_fine_tickets', compact('pendingTickets'));
    }


    public function ticketDetails(Request $request)
    {
        $refNo = $request->input('did');

        $ticket = DB::table('issued_fine_tickets')
            ->join('driver_list', 'issued_fine_tickets.license_id', '=', 'driver_list.license_id')
            ->leftJoin('vehicles', 'issued_fine_tickets.vehicle_no', '=', 'vehicles.vehicle_no')
            ->where('issued_fine_tickets.ref_no', $refNo)
            ->select('issued_fine_tickets.*', 'driver_list.driver_name', 'driver_list.contact_no', 'driver_list.license_type', 'vehicles.vehicle_type')
            ->first();

        if (!$ticket) {
            return '<p class="text-danger">No data found for this ticket.</p>';
        }

        return view('layouts.components.admin.modals.pending_ticket_modal_view', compact('ticket'))->render();
    }

    public function paidTicketDetails(Request $request)
    {
        $refNo = $request->input('ref_no');

        $ticket = DB::table('issued_fine_tickets')
            ->join('driver_list', 'issued_fine_tickets.license_id', '=', 'driver_list.license_id')
            ->leftJoin('vehicles', 'issued_fine_tickets.vehicle_no', '=', 'vehicles.vehicle_no')
            ->where('issued_fine_tickets.ref_no', $refNo)
            ->where('issued_fine_tickets.status', 'paid')
            ->select('issued_fine_tickets.*', 'driver_list.driver_name', 'driver_list.contact_no', 'driver_list.license_type', 'vehicles.vehicle_type')
            ->first();

        if (!$ticket) {
            return '<p class="text-danger">No data found for this ticket.</p>';
        }

        return view('layouts.components.admin.modals.paid_ticket_modal_view', compact('ticket'))->render();
    }


    public function payFine(Request $request)
    {
        $refNo = $request->input('ref_no');

        $updated = DB::table('issued_fine_tickets')->where('ref_no', $refNo)->update(['status' => 'paid']);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Ticket marked as paid.']);
        }

        return response()->json(['success' => false]);
    }

    public function viewPaidFines()
    {
        // This is block to avoid auto login 
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        $paidTickets = DB::table('issued_fine_tickets')
            ->join('driver_list', 'issued_fine_tickets.license_id', '=', 'driver_list.license_id')
            ->select('issued_fine_tickets.*', 'driver_list.driver_name')
            ->where('issued_fine_tickets.status', 'paid')
            ->get();

        return view('admin.paid_fine_tickets', compact('paidTickets'));
    }

    public function viewSecureTicket($token)
    {
        $ticket = DB::table('issued_fine_tickets')
            ->join('driver_list', 'issued_fine_tickets.license_id', '=', 'driver_list.license_id')
            ->leftJoin('vehicles', 'issued_fine_tickets.vehicle_no', '=', 'vehicles.vehicle_no')
            ->where('issued_fine_tickets.secure_token', $token)
            ->select(
                'issued_fine_tickets.*',
                'driver_list.driver_name',
                'driver_list.contact_no',
                'driver_list.license_type',
                'vehicles.vehicle_type'
            )
            ->first();

        if (!$ticket) {
            abort(404, 'Ticket not found or invalid link.');
        }

        return view('driver.view-ticket', compact('ticket'));
    }
}
