<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SoftwareMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_agent = $request->header("X-FINNECLE-SOFTWARE");
        if($user_agent == "true") {
            return $next($request);
        } else {
            Auth::logout();
            return redirect()->route('index')->with(['error_message'=>"Sorry, you can't access this from here. :("]);
        }
    }
}
