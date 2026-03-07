<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Websitemail;
use App\Models\Admin;
use Hash;
use Auth;

class AdminLoginController extends Controller
{
    public function index()
    {
        // $pass = Hash::make('12345678');
       
        return view('admin.login');
    }
    
    /* -------------------- Soumission du formulaire de connexion -------------------- */
    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin_home');
        } else {
            return back()->with('error', 'Incorrect email or password.');
        }
    }

    /* -------------------- Déconnexion -------------------- */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin_login');
    }
    
    
    
    public function forget_password()
    {
        return view('admin.forget_password');
    }
}
