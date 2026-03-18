<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Websitemail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerAuthController extends Controller
{
    public function signup()
    {
        return view('front.signup');
    }

    public function login()
    {
        return view('front.login');
    }

    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ];

        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect()->route('customer_home');
        } else {
            return redirect()->route('customer_login')->with('error', 'Information is not correct!');
        }
    }
    
    
    
    
    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('customer_login');
    }
}
