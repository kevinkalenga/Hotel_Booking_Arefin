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

    public function edit($id)
    {
        // check all the items from the slide
        $feature_data = Feature::where('id', $id)->first();

        return view('admin.feature_edit', compact('feature_data'));
    }

    
    public function update(Request $request, $id)
    {
          $request->validate([
             'icon' => 'required',
             'heading' => 'required'
          ]);

        $obj = Feature::findOrFail($id);
        $obj->icon = $request->icon;
        $obj->heading = $request->heading;
        $obj->text = $request->text;
       
        $obj->save();

        return redirect()->back()->with('success', 'Feature updated successfully');
    }


    public function delete($id)
    {
        $single_data = Feature::where('id', $id)->first();
        $single_data->delete();

         return redirect()->back()->with('success', 'Feature deleted successfully');
    }

}
