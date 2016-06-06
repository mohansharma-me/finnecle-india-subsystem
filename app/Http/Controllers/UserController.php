<?php

namespace App\Http\Controllers;

use App\ClearRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{

    public function __construct()
    {
        // Any Roles
        $this->middleware('role:any', [
            'only'=> ['getDashboard', 'getLogout']
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex() {
        // Redirect to Dashboard if user already logged in...
        if(Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Otherwise show login page or say front page
        return view('index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getForgotPassword() {
        // Render forgot password view
        return view('forgot-password');
    }

    /**
     * @param Request $request
     */
    public function postLogin(Request $request) {

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        // Attempt to validate credentials
        if(Auth::attempt(['email'=>$request["email"], 'password'=>$request["password"]])) {
            return redirect()->route('dashboard')->with(['welcome_message'=> "You're welcome."]);
        } else {
            return redirect()->route('index')->with(['error_message'=>"Sorry, e-mail address and password didn't matched."])->withInput(Input::all());
        }
    }

    // ANY ROLES ///////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getLogout() {
        Auth::logout();
        return redirect()->route('index')->with(['success_message'=>'You are successfully logged out.']);
    }

    public function getDashboard() {
        $dashboardViewName = Auth::user()->getRole('dashboard');
        if(view()->exists($dashboardViewName)) {

            $data = [];

            switch(Auth::user()->role) {
                case "donator":
                    $data["clear_requests"] = Auth::user()->isCenter()->clear_requests()->orderBy('id', 'desc')->paginate(30);
                    break;
                case "admin":
                    $data["clear_requests"] = ClearRequest::orderBy('id', 'desc')->paginate(30);
                    break;
                case "cashier":
                    $data["clear_requests"] = Auth::user()->isCenter()->clear_requests()->orderBy('id', 'desc')->paginate(30);
                    break;
            }

            return view($dashboardViewName, $data);
        } else {
            return view('errors.role_not_found');
        }
    }

    // TEST ROLES //////////////////////////////////////////////////////////////////////////////////////////////////////



}
