<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Namshi\JOSE\JWT;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    public function __construct()
    {

        $this->middleware('api:any', [
            'except'=> ['postAuth']
        ]);

    }

    public function getAuth(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json(["user"=>$user]);
    }

    public function postAuth(Request $request) {

        // grab credentials from the request
        $credentials = [ 'email'=> $request->input('email'), 'password'=>$request->input('password') ];

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error_message' => 'E-mail address and password combination didn\'t work, try again.'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error_message' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        $user = JWTAuth::setToken($token)->authenticate();
        if($user) {
            return response()->json(['token'=>$token, 'success_message'=>'You are successfully authenticated', 'flag'=>true, 'user'=>$user]);
        } else {
            return response()->json(['error_message'=>"User not found, try again"]);
        }

    }

    public function getDashboard(Request $request) {

        $user = JWTAuth::parseToken()->authenticate();

        $view_name = "api.".$user->role.".dashboard";
        if(view()->exists($view_name)) {
            return view($view_name);
        } else {
            abort(404);
        }

    }

    //////////////////////////////////////////
    /////////////// DONATOR //////////////////
    //////////////////////////////////////////


    //////////////////////////////////////////
    /////////////// CASHIER //////////////////
    //////////////////////////////////////////


}
