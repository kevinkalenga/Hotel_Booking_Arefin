<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class AdminTestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::get();
        return view('admin.testimonial_view', compact('testimonials'));
    }

    public function add()
    {
        return view('admin.testimonial_add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'name' => 'required',
            'designation' => 'required',
            'comment' => 'required',
        ]);

        $final_name = null;

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('photo')->move(public_path('uploads/'), $final_name);
        }

        $obj = new Testimonial();
        $obj->photo = $final_name;
        $obj->name = $request->name;
        $obj->designation = $request->designation;
        $obj->comment = $request->comment;
        $obj->save();

        return redirect()->back()->with('success', 'Testimonial added successfully');
    }


    public function edit($id)
    {
        $testimonial_data = Testimonial::findOrFail($id);
        return view('admin.testimonial_edit', compact('testimonial_data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'name' => 'required',
            'designation' => 'required',
            'comment' => 'required',
        ]);

        $obj = Testimonial::findOrFail($id);

        if ($request->hasFile('photo')) {
            // supprimer l'ancienne photo si elle existe
            if ($obj->photo && file_exists(public_path('uploads/' . $obj->photo))) {
                unlink(public_path('uploads/' . $obj->photo));
            }

            $ext = $request->file('photo')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('photo')->move(public_path('uploads/'), $final_name);

            $obj->photo = $final_name;
        }

        // ne pas toucher à la photo si aucune nouvelle n’est envoyée
        $obj->name = $request->name;
        $obj->designation = $request->designation;
        $obj->comment = $request->comment;
        $obj->save();

        return redirect()->back()->with('success', 'Testimonial updated successfully');
    }

    public function delete($id)
    {
        $feature_data = Testimonial::where('id', $id)->first();
        
        $feature_data->delete();

         return redirect()->back()->with('success', 'Feature deleted successfully');
    }

}
