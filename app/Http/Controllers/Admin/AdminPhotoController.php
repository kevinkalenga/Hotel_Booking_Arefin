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


    public function edit($id)
    {
        // check all the items from the slide
        $photo_data = Photo::where('id', $id)->first();

        return view('admin.photo_edit', compact('photo_data'));
    }

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
          
        ]);

        $obj = Photo::findOrFail($id);

        if ($request->hasFile('photo')) {
            // delete old photo if exists
            if ($obj->photo && file_exists(public_path('uploads/' . $obj->photo))) {
                unlink(public_path('uploads/' . $obj->photo));
            }

            $ext = $request->file('photo')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('photo')->move(public_path('uploads/'), $final_name);

            $obj->photo = $final_name;
        }

        $obj->caption = $request->caption;
       
        $obj->save();

        return redirect()->back()->with('success', 'Photo updated successfully');
    }

    public function delete($id)
    {
        $single_data = Photo::where('id', $id)->first();
        unlink(public_path('uploads/'.$single_data->photo));
        $single_data->delete();

         return redirect()->back()->with('success', 'Photo deleted successfully');
    }


}
