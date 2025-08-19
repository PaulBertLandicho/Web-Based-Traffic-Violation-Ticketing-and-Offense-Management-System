<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckIfEnforcerLocked
{
    public function handle($request, Closure $next)
    {
        if (Session::has('police_id')) {
            $enforcer = DB::table('traffic_enforcers')
                ->where('police_id', session('police_id'))
                ->first();

            if ($enforcer && $enforcer->is_locked) {
                Session::flush();
                return redirect()->route('enforcer.login')->with('error', 'Your account is locked.');
            }
        }

        return $next($request);
    }
}
