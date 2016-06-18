<?php

namespace App\Http\Controllers;

use App\ClearRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function getDashboard(Request $request) {
        $dashboardViewName = Auth::user()->getRole('dashboard');
        if(view()->exists($dashboardViewName)) {

            $data = [];

            switch(Auth::user()->role) {
                case "donator":
                    $data["clear_requests"] = Auth::user()->isCenter()->clear_requests()->orderBy('id', 'desc')->paginate(30);
                    break;
                case "admin":
                    $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', \Carbon\Carbon::now()->format('d-m-Y 00:00:00'));
                    $toDate = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', \Carbon\Carbon::now()->addDays(1)->format('d-m-Y 23:59:59'));

                    $query = ClearRequest::select(
                        "clear_requests.*",
                        DB::raw("centers.id as center_id, centers.user_id as center_user_id, centers.name as center_name"),
                        DB::raw("users.role as user_role")
                    );
                    $query->join('centers', 'centers.id','=','clear_requests.center_id');
                    $query->join('users','users.id','=','centers.user_id');

                    if($request->has('center')) {
                        $query->where('centers.name','like','%'.$request->center.'%');
                    }

                    if($request->has('fromDate') && $request->has('toDate')) {
                        try {
                            $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->fromDate." 00:00:00");
                            $toDate = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->toDate." 23:59:59");
                        } catch(\Exception $e) {}

                        $diffDays = $toDate->diffInDays($fromDate);
                        if($diffDays >= 0) {
                            $query->whereBetween('clear_requests.created_at', [$fromDate, $toDate]);
                        }
                    }

                    if($request->has('role')) {
                        $query->where('users.role','=',$request->role);
                    }

                    $data["clear_requests"] = $query->paginate(30);
                    $data["fromDate"] = $fromDate;
                    $data["toDate"] = $toDate;
                    $data["centerSearch"] = $request->center;
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
