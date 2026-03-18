<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Websitemail;
use App\Models\Subscriber;

class SubscriberController extends Controller
{
    public function send_email(Request $request)
    {
        
      $validator = \Validator::make($request->all(),[
      
        'email' => 'required|email',
       
      ]);

       if(!$validator->passes())
       {
          return response()->json(['code'=>0,'error_message'=>$validator->errors()->toArray()]);
        }else {
          
            // Check if email already exists
             $existing = Subscriber::where('email', $request->email)->first();
             if ($existing) {
                 return redirect()->back()->with('error', 'This email is already subscribed!');
             }
         
          $token = hash('sha256', time());
          
          
          $obj = new Subscriber();
          $obj->email = $request->email;
          $obj->token = $token;
          $obj->status = 0;
          $obj->save();

          $verification_link = url('subscriber/verify/'.$request->email.'/'.$token);
          
          
          
          // Send email
            $subject = 'Subscriber Verification';
            $message  = 'Please click on the link below to confirm subscription: <br>';
            $message .= '<a href="'.$verification_link.'">';
            $message .= $verification_link;
            $message .= '</a>';
          
          // \Mail::to($admin_email)->send(new Websitemail($subject, $message));

          try {
                  \Mail::to($request->email)->send(new \App\Mail\Websitemail($subject, $message));

                  return response()->json(['code' => 1, 'success_message' => 'Email sent successfully']);
              } catch (\Exception $e) {
                  return response()->json([
                      'code' => 0,
                      'error_message' => 'Error sending : ' . $e->getMessage()
                  ]);
              }

           
          
          return response()->json(['code'=>1,'success_message'=>'Please check your email to confirm subscription']);
         }
    }

    public function verify()
    {
      
    }
}
