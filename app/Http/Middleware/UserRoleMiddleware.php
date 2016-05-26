<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $anyRole = $role == "any";
        if($anyRole || Auth::user()->hasRole($role)) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with(['master_msg' => "Sorry, you're not allowed there.", 'master_flag'=>false]);
    }
}
