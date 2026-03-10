<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class AdminSliderController extends Controller
{
    public function index()
    {
        $slides = Slider::get();
        return view('admin.slide_view', compact('slides'));
    }

    public function add()
    {
        return view('admin.slide_add');
    }

    public function store(Request $request)
    {
            $request->validate([
            'photo' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
             'heading' => 'nullable|string|max:255',
            'text' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
        ]);

        $final_name = null;

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->extension();
            $finale_name = time().'.'.$ext;
            $request->file('photo')->move(public_path('uploads/'), $finale_name);

            $obj = new Slider();
            $obj->photo = $finale_name;
            $obj->heading = $request->heading;
            $obj->text = $request->text;
            $obj->button_text = $request->button_text;
            $obj->button_url = $request->button_url;
            $obj->save();
        } else {
            return back()->withErrors(['photo' => 'No file uploaded']);
        }

        return redirect()->back()->with('success', 'Slider is added Successfully');
    }


    public function edit($id)
    {
        // check all the items from the slide
        $slide_data = Slider::where('id', $id)->first();

        return view('admin.slide_edit', compact('slide_data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'heading' => 'nullable|string|max:255',
            'text' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            // 'button_url' => 'nullable|url|max:255',
        ]);

        $obj = Slider::findOrFail($id);

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

        $obj->heading = $request->heading;
        $obj->text = $request->text;
        $obj->button_text = $request->button_text;
        $obj->button_url = $request->button_url;
        $obj->save();

        return redirect()->back()->with('success', 'Slider updated successfully');
    }


    public function delete($id)
    {
        $single_data = Slider::where('id', $id)->first();
        unlink(public_path('uploads/'.$single_data->photo));
        $single_data->delete();

         return redirect()->back()->with('success', 'Slider deleted successfully');
    }
}
