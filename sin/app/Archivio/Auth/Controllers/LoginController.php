<?php

namespace App\Auth\Controllers;

use App\Core\Controllers\BaseController as Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //  protected $redirectTo = '/home';

    public function redirectPath()
    {
      if(Auth::user()->hasRole('admin'))
        return route('admin.backup');//"/roles";
      else if (Auth::user()->hasRole(['biblioteca-amm','biblioteca-ope']))
          return route("biblioteca");
      else if (Auth::user()->hasRole(['meccanica-amm','meccanica-ope']))
          return route("officina.index");
      else
         return route("home");
    }

   

    // By default, Laravel uses the email field for authentication.
    // If you would like to customize this, you may define a username method on your LoginController:
    //
    public function username()
    {
        return 'username';
    }
}
