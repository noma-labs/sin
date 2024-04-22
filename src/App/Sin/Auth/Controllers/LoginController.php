<?php

namespace App\Auth\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    public function redirectPath()
    {
        if (Auth::user()->hasRole('admin')) {
            return route('admin.backup');
        } elseif (Auth::user()->hasRole(['biblioteca-amm', 'biblioteca-ope'])) {
            return route('biblioteca');
        } elseif (Auth::user()->hasRole(['meccanica-amm', 'meccanica-ope'])) {
            return route('officina.index');
        } else {
            return route('home');
        }
    }

    // By default, Laravel uses the email field for authentication.
    // If you would like to customize this, you may define a username method on your LoginController:
    //
    public function username()
    {
        return 'username';
    }
}
