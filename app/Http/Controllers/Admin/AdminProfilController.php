<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\Admin;

class AdminProfilController extends Controller
{
    public function index()
    {
        return view('admin.profile');
    }

    public function profile_submit(Request $request)
    {
       $request->validate([
             'name' => 'required',
             'email' => 'required|email',
             'password' => 'nullable|min:6|confirmed',
        ]);

        
        
       
        $admin_data = Admin::find(Auth::guard('admin')->id());
        // Mettre à jour le mot de passe seulement si rempli
        if ($request->password) {
              $admin_data->password = Hash::make($request->password);
        }
      
        //  photo

        if ($request->hasFile('photo')) {
           $request->validate([
            'photo' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
           ]);

            // Supprime l'ancienne photo seulement si ce n'est pas une photo par défaut
            $defaultPhotos = ['user.jpg', 'admin.jpeg', 'default.png'];
            if ($admin_data->photo && !in_array($admin_data->photo, $defaultPhotos) && file_exists(public_path('uploads/' . $admin_data->photo))) {
                unlink(public_path('uploads/' . $admin_data->photo));
            }

             // Sauvegarde la nouvelle photo
             $final_name = 'admin_' . time() . '.' . $request->photo->extension();
             $request->photo->move(public_path('uploads'), $final_name);
             $admin_data->photo = $final_name;
        }


       // Update
        $admin_data->name  = $request->name;
        $admin_data->email = $request->email;
        $admin_data->save();

        Auth::guard('admin')->setUser($admin_data->fresh());

        return redirect()->back()->with('success', 'Profile updated successfully.');

    }

}
