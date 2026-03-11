<?php

namespace App\Http\Controllers\Admin;
use App\Models\Feature;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminFeatureController extends Controller
{
    public function index()
    {
        $features = Feature::get();
        return view('admin.feature_view', compact('features'));
    }

    public function add()
    {
        return view('admin.feature_add');
    }
    public function store(Request $request)
    {
            $request->validate([
             'icon' => 'required',
             'heading' => 'required'
          ]);

      

          $obj = new Feature();
          $obj->icon = $request->icon;
          $obj->heading = $request->heading;
          $obj->text = $request->text;
          $obj->save();
         

        return redirect()->back()->with('success', 'Feature is added Successfully');
    }

    

}
