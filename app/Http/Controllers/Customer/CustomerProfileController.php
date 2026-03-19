<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerProfileController extends Controller
{
    /* -------------------- Page de Profie -------------------- */
    public function index()
    {
        return view('customer.profile');
    }

    public function profile_submit(Request $request)
    {
       $request->validate([
             'name' => 'required',
             'email' => 'required|email',
             'password' => 'nullable|min:6|confirmed',
        ]);

        
        
       
        $customer_data = Customer::find(Auth::guard('customer')->id());
        // Mettre à jour le mot de passe seulement si rempli
        if ($request->password) {
              $customer_data->password = Hash::make($request->password);
        }
      
        //  photo

        if ($request->hasFile('photo')) {
           $request->validate([
            'photo' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
           ]);

            // Supprime l'ancienne photo seulement si ce n'est pas une photo par défaut
            $defaultPhotos = ['user.jpg', 'default.png'];
            if ($customer_data->photo && !in_array($customer_data->photo, $defaultPhotos) && file_exists(public_path('uploads/' . $customer_data->photo))) {
                unlink(public_path('uploads/' . $customer_data->photo));
            }

             // Sauvegarde la nouvelle photo
             $final_name = 'customer_' . time() . '.' . $request->photo->extension();
             $request->photo->move(public_path('uploads'), $final_name);
             $customer_data->photo = $final_name;
        }


        // Update
        $customer_data->name  = $request->name;
        $customer_data->email = $request->email;
        $customer_data->phone = $request->phone;
        $customer_data->country = $request->country;
        $customer_data->address = $request->address;
        $customer_data->state = $request->state;
        $customer_data->city = $request->city;
        $customer_data->zip = $request->zip;
        $customer_data->save();

        Auth::guard('customer')->setUser($customer_data->fresh());

        return redirect()->back()->with('success', 'Profile updated successfully.');

    }
}
