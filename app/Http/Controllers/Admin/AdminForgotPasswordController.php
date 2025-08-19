<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrafficAdmin;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;


class AdminForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.admin-forgot-password');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $admin = TrafficAdmin::where('admin_email', $request->email)->first();

        if (!$admin) {
            return redirect()->back()->withErrors(['email' => 'Incorrect email address!'])->withInput();
        }

        $code = rand(111111, 999999);
        $admin->code = $code;
        $admin->save();

        // Send mail
        try {
            Mail::raw("Your verification code is $code", function ($message) use ($admin) {
                $message->to($admin->admin_email)
                    ->subject('Email Verification Code')
                    ->from('landichopaulbert17@gmail.com');
            });

            session(['admin_email' => $admin->admin_email]);
            return redirect()->route('admin.verification.code')->with('success', 'We\'ve sent a verification code to your email.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'Failed while sending the code!']);
        }
    }

    public function showVerifyCodeForm()
    {
        return view('admin.auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $adminEmail = session('admin_email');

        $admin = TrafficAdmin::where('admin_email', $adminEmail)
            ->where('code', $request->code)
            ->first();

        if (!$admin) {
            return back()->withErrors(['code' => 'Invalid verification code!']);
        }

        // Clear code and proceed
        $admin->code = null;
        $admin->save();

        session(['admin_verified_email' => $adminEmail]);

        return redirect()->route('admin.reset.password')->with('success', 'Verification successful. You can now reset your password.');
    }

    public function showResetForm()
    {
        if (!session('admin_verified_email')) {
            return redirect()->route('admin.forgot');
        }

        return view('admin.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = TrafficAdmin::where('admin_email', session('admin_verified_email'))->first();

        if (!$admin) {
            return redirect()->route('admin.forgot')->withErrors(['email' => 'Session expired.']);
        }

        $admin->admin_password = Hash::make($request->password);
        $admin->save();

        session()->forget(['admin_verified_email', 'admin_email']);

        return redirect()->route('admin.login')->with('success', 'Password reset successfully.');
    }
}
