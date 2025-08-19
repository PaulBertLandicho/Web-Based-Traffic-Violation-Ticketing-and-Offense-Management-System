<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class SmsLogController extends Controller
{
    public function sms()
    {
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login first.');
        }

        $smsLogs = DB::table('sms_logs')->orderByDesc('created_at')->get();

        return view('admin.sms_activity_log', compact('smsLogs'));
    }

    public function sendSMSViaVonage($licenseId)
    {
        if (empty($licenseId)) {
            return response()->json(['sent_status' => 'error', 'message' => 'Missing license ID.']);
        }

        $driver = DB::table('driver_list')->where('license_id', $licenseId)->first();
        $fine = DB::table('issued_fine_tickets')->where('license_id', $licenseId)->orderByDesc('ref_no')->first();

        if (!$driver || !$fine) {
            return response()->json(['sent_status' => 'error', 'message' => 'Driver or Fine not found.']);
        }

        $vonage = new Client(new Basic(env('VONAGE_KEY'), env('VONAGE_SECRET')));
        $viewUrl = url('/driver/view-ticket/' . $fine->secure_token);

        $text = <<<TEXT
ICTPMO Traffic Violation Notice
Date: {$fine->issued_date}

Dear {$driver->driver_name},
You have been issued a traffic violation notice.
License No: {$licenseId}

To view full details and the current status:
{$viewUrl}

Please settle within 7 days.
Thank you!
TEXT;

        try {
            $response = $vonage->sms()->send(new SMS($driver->contact_no, 'ICTPMO', $text));
            $message = $response->current();
            $status = $message->getStatus();

            DB::table('sms_logs')->insert([
                'license_id' => $licenseId,
                'contact_no' => $driver->contact_no,
                'message' => $text,
                'sent_status' => $status == 0 ? 'sent' : 'failed',
                'response' => $message->getStatus() == 0 ? 'Message sent successfully.' : 'Failed to send. Code: ' . $status,
                'created_at' => now()
            ]);

            return response()->json([
                'sent_status' => $status == 0 ? 'success' : 'failed',
                'message' => $status == 0
                    ? 'SMS sent successfully to ' . $driver->contact_no
                    : 'Failed to send SMS. Code: ' . $status,
                'view_url' => $viewUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'sent_status' => 'error',
                'message' => 'SMS failed: ' . $e->getMessage()
            ]);
        }
    }

    public function resend(Request $request)
    {
        $smsId = $request->input('sms_id');

        $log = DB::table('sms_logs')->where('id', $smsId)->first();
        if (!$log) {
            return redirect()->route('sms.logs')->with('error', 'SMS log not found.');
        }

        $driver = DB::table('driver_list')->where('license_id', $log->license_id)->first();
        $fine = DB::table('issued_fine_tickets')
            ->where('license_id', $log->license_id)
            ->orderByDesc('ref_no')
            ->first();

        if (!$driver || !$fine) {
            return redirect()->route('sms.logs')->with('error', 'Driver or Fine record missing.');
        }

        $vonage = new Client(new Basic(env('VONAGE_KEY'), env('VONAGE_SECRET')));
        $viewUrl = url('/driver/view-ticket/' . $fine->secure_token);

        $text = <<<TEXT
ICTPMO Traffic Violation Notice
Date: {$fine->issued_date}

Dear {$driver->driver_name},
You have been issued a traffic violation notice.
License No: {$driver->license_id}

To view full details and the current status:
{$viewUrl}

Please settle within 7 days.
Thank you!
TEXT;

        try {
            $response = $vonage->sms()->send(new SMS($driver->contact_no, 'ICTPMO', $text));
            $message = $response->current();
            $status = $message->getStatus();

            DB::table('sms_logs')->where('id', $smsId)->update([
                'sent_status' => $status == 0 ? 'sent' : 'failed',
                'response' => $status == 0 ? 'Message resent successfully.' : 'Failed to resend. Code: ' . $status,
                'created_at' => now()
            ]);

            return redirect()->route('sms.logs')->with(
                $status == 0 ? 'success' : 'error',
                $status == 0 ? 'SMS resent and sent_status updated.' : 'SMS resend failed.'
            );
        } catch (\Exception $e) {
            DB::table('sms_logs')->where('id', $smsId)->update([
                'sent_status' => 'failed',
                'response' => 'Exception: ' . $e->getMessage(),
                'created_at' => now()
            ]);

            return redirect()->route('sms.logs')->with('error', 'Failed to resend SMS: ' . $e->getMessage());
        }
    }
}
