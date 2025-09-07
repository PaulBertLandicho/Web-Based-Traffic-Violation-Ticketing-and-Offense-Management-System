<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ViolationController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function create()
    {
        return $this->view();
    }

    public function view()
    {
        // This is block to avoid auto login 
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        $violations = DB::table('traffic_violations')
            ->where('is_archived', 0)
            ->get();

        return view('admin.traffic_violation', compact('violations'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'violationid' => 'required|unique:traffic_violations,violation_id',
            'violationtype' => 'required|string',
            'violationamount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $violation_id = $request->violationid;

        DB::table('traffic_violations')->insert([
            'violation_id' => $violation_id,
            'violation_type' => $request->violationtype,
            'violation_amount' => $request->violationamount,
            'created_at' => Carbon::now(),
        ]);

        $this->firebase->getDatabase()
            ->getReference('traffic_violations/' . $violation_id)
            ->set([
                'violation_id' => $violation_id,
                'violation_type' => $request->violationtype,
                'violation_amount' => $request->violationamount,
                'created_at' => Carbon::now()->toDateTimeString(),
            ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Violation added successfully',
                'violation' => [
                    'violation_id' => $violation_id,
                    'violation_type' => $request->violationtype,
                    'violation_amount' => $request->violationamount,
                ]
            ]);
        }

        return response()->json([
            'success' => '✅ Violation added successfully',
            'violation' => [
                'violation_id' => $violation_id,
                'violation_type' => $request->violationtype,
                'violation_amount' => $request->violationamount,
            ]
        ]);
    }


    public function getViolationDetails(Request $request)
    {
        $violation = DB::table('traffic_violations')
            ->where('violation_id', $request->did)
            ->first();

        return response()->json([
            'traffic_violations' => $violation,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'violation_id' => 'required|exists:traffic_violations,violation_id',
            'violation_type' => 'required|string',
            'violation_amount' => 'required|numeric',
        ]);

        // Update in MySQL
        DB::table('traffic_violations')
            ->where('violation_id', $validated['violation_id'])
            ->update([
                'violation_type' => $validated['violation_type'],
                'violation_amount' => $validated['violation_amount']
            ]);

        // Update in Firebase
        $this->firebase->getDatabase()
            ->getReference('traffic_violations/' . $validated['violation_id'])
            ->update([
                'violation_type' => $validated['violation_type'],
                'violation_amount' => $validated['violation_amount']
            ]);

        return response()->json(['success' => '✅ Violation updated successfully']);
    }

    public function archive(Request $request)
    {
        $violationId = $request->aid;

        $violation = DB::table('traffic_violations')
            ->where('violation_id', $violationId)
            ->first();

        if (!$violation) {
            return response()->json(['error' => 'Traffic Violation not found.']);
        }

        DB::table('traffic_violations')
            ->where('violation_id', $violationId)
            ->update(['is_archived' => 1]);

        $this->firebase->getDatabase()
            ->getReference('traffic_violations/' . $violationId)
            ->update(['is_archived' => true]);

        return response()->json(['success' => 'Violation archived successfully']);
    }

    public function archived()
    {
        $violations = DB::table('traffic_violations')
            ->where('is_archived', 1)
            ->get();

        return response()->json(['violations' => $violations]);
    }

    public function restore(Request $request)
    {
        $violationId = $request->rid;

        $violation = DB::table('traffic_violations')->where('violation_id', $violationId)->first();

        if (!$violation) {
            return response()->json(['error' => 'Violation not found.']);
        }

        DB::table('traffic_violations')
            ->where('violation_id', $violationId)
            ->update(['is_archived' => 0]);

        // Return restored violation details
        return response()->json([
            'success' => 'Violation restored successfully!',
            'violation' => [
                'violation_id' => $violation->violation_id,
                'violation_type' => $violation->violation_type,
                'violation_amount' => $violation->violation_amount
            ]
        ]);
    }
}
