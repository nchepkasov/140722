<?php 

namespace App\Controllers\Auth;

class Login extends \App\Controller
{
    public function showLoginForm ()
    {
    }

    public function login ($err = false)
    {
        return (new \App\BasicView ("auth/login"))->render ([ 
            "err" => $err 
        ]);
    }

    public function doLogin ($req)
    {
        if (\App\Services\Auth::doLogin ($req->login, $req->password))
        {
            \App\Helpers\Redirect::to ("/");
        }
        else
        {
            \App\Helpers\Redirect::to ("/login?err=1");
        }
    }

    public function doLogout ()
    {
        \App\Services\Auth::doLogout ();
        
        \App\Helpers\Redirect::to ("/");
    }
}