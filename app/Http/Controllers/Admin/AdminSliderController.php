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
}
