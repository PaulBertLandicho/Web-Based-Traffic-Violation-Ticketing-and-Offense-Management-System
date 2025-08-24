<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\FirebaseService;
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

        $violations = DB::table('traffic_violations')->get();
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $violation_id = $request->violationid;

        // Save to MySQL
        DB::table('traffic_violations')->insert([
            'violation_id' => $violation_id,
            'violation_type' => $request->violationtype,
            'violation_amount' => $request->violationamount,
            'created_at' => Carbon::now(),
        ]);

        // Save to Firebase
        $this->firebase->getDatabase()
            ->getReference('traffic_violations/' . $violation_id)
            ->set([
                'violation_id' => $violation_id,
                'violation_type' => $request->violationtype,
                'violation_amount' => $request->violationamount,
                'created_at' => Carbon::now()->toDateTimeString(),
            ]);

        return redirect()->route('violation.create')->with('success', 'Violation and Driver Details Added Successfully!');
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

    public function delete(Request $request)
    {
        $violationId = $request->did;

        // Delete from MySQL
        $deleted = DB::table('traffic_violations')->where('violation_id', $violationId)->delete();

        if ($deleted) {
            // Delete from Firebase
            $this->firebase->getDatabase()
                ->getReference('traffic_violations/' . $violationId)
                ->remove();

            return response()->json(['success' => 'Violation deleted successfully']);
        } else {
            return response()->json(['error' => 'Violation not found.']);
        }
    }
}
