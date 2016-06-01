<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiMiddleware
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
        try {

            if($role == "guest") {
                return $next($request);
            }

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error_message'=>'user_not_found'], 404);
            } else {

                if($role == "any") {
                    return $next($request);
                }

                if($role && $user->hasRole($role)) {
                    return $next($request);
                } else {
                    return response()->json(['error_message'=>'unauthenticated_access'], 401);
                }
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['error_message'=>'token_expired'], $e->getStatusCode());

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['error_message'=>'token_invalid'], $e->getStatusCode());

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['error_message'=>'token_absent'], $e->getStatusCode());

        }
    }
}
