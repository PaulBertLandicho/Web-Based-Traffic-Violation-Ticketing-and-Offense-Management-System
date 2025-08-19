<?php

namespace App\Http\Controllers\Enforcer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TrafficEnforcer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class EnforcerForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('enforcer.auth.enforcer-forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $enforcer = TrafficEnforcer::where('enforcer_email', $request->email)->first();

        if (!$enforcer) {
            return back()->with('error', 'Email not found in our records.');
        }

        $code = rand(111111, 999999);
        $enforcer->code = $code;
        $enforcer->save();

        Mail::raw("Your verification code is: $code", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Enforcer Password Reset Code');
        });

        session(['enforcer_email' => $request->email]);

        return redirect()->route('enforcer.verify.form')->with('success', 'Verification code sent to your email.');
    }

    public function showVerifyCodeForm()
    {
        return view('enforcer.auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $enforcer = TrafficEnforcer::where('enforcer_email', session('enforcer_email'))
            ->where('code', $request->code)
            ->first();

        if (!$enforcer) {
            return back()->with('error', 'Invalid verification code.');
        }

        session(['code_verified' => true]);

        return redirect()->route('enforcer.reset.form');
    }

    public function showResetForm()
    {
        if (!session('code_verified')) {
            return redirect()->route('enforcer.forgot.form');
        }

        return view('enforcer.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $email = session('enforcer_email');
        $enforcer = TrafficEnforcer::where('enforcer_email', $email)->first();

        if (!$enforcer) {
            return redirect()->route('enforcer.forgot.form')->with('error', 'Session expired or enforcer not found.');
        }

        // Update password
        $enforcer->enforcer_password = Hash::make($request->password);
        $enforcer->code = rand(111111, 999999); // <-- generate new 6-digit code
        $enforcer->save();
        // $enforcer->enforcer_password = Hash::make($request->password);
        // $enforcer->code = null;
        // $enforcer->save();

        // Log in the enforcer
        Auth::login($enforcer);

        // Clear session
        session()->forget(['enforcer_email', 'code_verified']);

        // Redirect to dashboard
        return redirect()->route('enforcer.enforcer-dashboard')->with('success', 'Password updated and logged in successfully.');
    }
}
