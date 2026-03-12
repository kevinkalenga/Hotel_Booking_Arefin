<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Photo;

class AdminPhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::get();
        return view('admin.photo_view', compact('photos'));
    }

    public function add()
    {
        return view('admin.photo_add');
    }
    public function store(Request $request)
    {
            $request->validate([
            'photo' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
          
        ]);

         $final_name = null;

       

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->extension();
            $finale_name = time().'.'.$ext;
            $request->file('photo')->move(public_path('uploads/'), $finale_name);

            $obj = new Photo();
            $obj->photo = $finale_name;
            $obj->caption = $request->caption;
            $obj->save();
        } else {
            return back()->withErrors(['photo' => 'No file uploaded']);
        }

        return redirect()->back()->with('success', 'Photo is added Successfully');
    }
}
