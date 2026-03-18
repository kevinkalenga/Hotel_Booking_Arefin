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

    public function signup_submit(Request $request)
    {
         $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'password' => 'required',
            'retype_password' => 'required|same:password'
        ]);

        
         $token = hash('sha256', time());
         $password = Hash::make($request->password);
          $verification_link = url('signup-verify/'.$request->email.'/'.$token);
          
          $obj = new Customer();
          $obj->name = $request->name;
          $obj->email = $request->email;
          $obj->password = $password;
          $obj->token = $token;
          $obj->status = 0;
          $obj->save();
          
          
          
          // Send email
            $subject = 'Sign Up Verification';
            $message  = 'Please click on the link below to confirm sign up process: <br>';
            $message .= '<a href="'.$verification_link.'">';
            $message .= $verification_link;
            $message .= '</a>';
        

            \Mail::to($request->email)->send(new Websitemail($subject, $message));
        

        return redirect()->back()->with('success', 'To complete the signup, please check your email and click on the link.');

       
    }



    public function signup_verify()
    {
      
      
    
    
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
