<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class AdminSettingController extends Controller
{
  public function index()
  {
    $setting_data = Setting::where('id', 1)->first();
    return view('admin.setting', compact('setting_data'));
  }

  public function update(Request $request)
  {
       $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
           
        ]);

        $obj = Setting::where('id', 1)->first();

        if ($request->hasFile('logo')) {
            // delete old photo if exists
            if ($obj->logo && file_exists(public_path('uploads/' . $obj->logo))) {
                unlink(public_path('uploads/' . $obj->logo));
            }

            $ext = $request->file('logo')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('logo')->move(public_path('uploads/'), $final_name);

            $obj->logo = $final_name;
        }
        if ($request->hasFile('favicon')) {
            // delete old photo if exists
            if ($obj->favicon && file_exists(public_path('uploads/' . $obj->favicon))) {
                unlink(public_path('uploads/' . $obj->favicon));
            }

            $ext = $request->file('favicon')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('favicon')->move(public_path('uploads/'), $final_name);

            $obj->favicon = $final_name;
        }

        $obj->top_bar_phone = $request->top_bar_phone;
        $obj->top_bar_email = $request->top_bar_email;
        $obj->home_feature_status = $request->home_feature_status;
        $obj->home_latest_post_total = $request->home_latest_post_total;
        $obj->home_latest_post_status = $request->home_latest_post_status;
        $obj->footer_address = $request->footer_address;
        $obj->footer_phone = $request->footer_phone;
        $obj->footer_email = $request->footer_email;
        $obj->copyright = $request->copyright;
        $obj->facebook = $request->facebook;
        $obj->twitter = $request->twitter;
        $obj->instagram = $request->instagram;
        $obj->linkedin = $request->linkedin;
        $obj->pinterest = $request->pinterest;
        $obj->analytic_id = $request->analytic_id;

  
        $obj->save();

        return redirect()->back()->with('success', 'Setting updated successfully');
  }
}
