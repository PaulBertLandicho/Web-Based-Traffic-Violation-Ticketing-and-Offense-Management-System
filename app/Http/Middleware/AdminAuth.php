<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('admin_email')) {
            return redirect('/admin-login')->with('error', 'Please login as admin first.');
        }

        return $next($request);
    }
}
