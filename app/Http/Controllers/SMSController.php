<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use App\Models\Driver;
use App\Models\IssuedFineTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Vonage\SMS\Message\SMS as VonageSMS;

class SMSController extends Controller
{
    public function sendSMS($license_id)
    {
        try {
            // 1️⃣ Fetch Driver Details
            $driver = Driver::where('license_id', $license_id)->first();
            if (!$driver) {
                return response()->json([
                    'sent_status' => 'failed',
                    'message' => '❌ Driver not found in the database.'
                ]);
            }

            if (empty($driver->contact_no)) {
                return response()->json([
                    'sent_status' => 'failed',
                    'message' => '⚠️ Driver has no registered mobile number.'
                ]);
            }

            // 2️⃣ Validate and Format Contact Number
            $contactNumber = $driver->contact_no;
            if (preg_match('/^0\d{10}$/', $contactNumber)) {
                $contactNumber = '+63' . substr($contactNumber, 1);
            } elseif (!preg_match('/^\+63\d{10}$/', $contactNumber)) {
                return response()->json([
                    'sent_status' => 'failed',
                    'message' => '⚠️ Invalid phone number format. Use +63XXXXXXXXXX or 09XXXXXXXXX.'
                ]);
            }

            // 3️⃣ Get the Latest Fine Record
            $fine = IssuedFineTicket::where('license_id', $license_id)->latest()->first();
            if (!$fine) {
                return response()->json([
                    'sent_status' => 'failed',
                    'message' => '⚠️ No fine record found for this driver.'
                ]);
            }

            // 4️⃣ Compose the View Link
            $viewUrl = url('/driver/view-ticket/' . ($fine->secure_token ?? $fine->id));

            // 5️⃣ Create the SMS Message (use simple clear text — avoid special symbols)
            $fineDate = $fine->created_at
                ? $fine->created_at->format('M d, Y • h:i A')
                : now()->format('M d, Y • h:i A');
            $fineAmount = number_format($fine->total_amount ?? 0, 2);

            $messageText =
                "ICTPMO Traffic Violation Notice
Date: {$fineDate}

Dear {$driver->driver_name},
You have been issued a violation.

Violation: {$fine->violation_type}
Fine: PHP{$fineAmount}
Location: {$fine->place}

View Details here: {$viewUrl}

Please settle within 7 days.
ICTPMO Office";

            // 6️⃣ Send SMS via Vonage
            $credentials = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
            $vonage = new Client($credentials);

            $response = $vonage->sms()->send(
                new VonageSMS($contactNumber, env('VONAGE_SMS_FROM', 'VONAGE'), $messageText)
            );

            $message = $response->current();
            $status = $message->getStatus();

            // 7️⃣ Log the Message
            DB::table('sms_logs')->insert([
                'license_id' => $license_id,
                'contact_no' => $contactNumber,
                'message' => $messageText,
                'sent_status' => $status == 0 ? 'sent' : 'failed',
                'response' => $status == 0 ? 'Message sent successfully.' : 'Failed with code ' . $status,
                'created_at' => now(),
            ]);

            // 8️⃣ Handle Response
            if ($status == 0) {
                Log::info("✅ SMS sent successfully to {$contactNumber}");
                return response()->json([
                    'sent_status' => 'success',
                    'message' => "✅ Message successfully sent to {$contactNumber}",
                    'sms_content' => $messageText,
                    'note' => 'ℹ️ Reminder: On Vonage trial accounts, only verified numbers can receive the full message text and links.',
                ]);
            } else {
                Log::warning("⚠️ Vonage SMS failed with status code {$status}");
                return response()->json([
                    'sent_status' => 'failed',
                    'message' => "❌ Vonage SMS failed. Error code: {$status}",
                    'sms_content' => $messageText
                ]);
            }
        } catch (\Exception $e) {
            Log::error("❌ Vonage SMS Error: " . $e->getMessage());
            return response()->json([
                'sent_status' => 'error',
                'message' => "Server error: " . $e->getMessage()
            ]);
        }
    }
}
