<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Video;

class AdminVideoController extends Controller
{
    public function index()
    {
        $videos = Video::get();
        return view('admin.video_view', compact('videos'));
    }

    public function add()
    {
        return view('admin.video_add');
    }


    public function store(Request $request)
    {
            $request->validate([
            'video_id' => 'required',
             
        ]);

        $obj = new Video();
        $obj->video_id = $request->video_id;
        $obj->caption = $request->caption;
        $obj->save();

        return redirect()->back()->with('success', 'Video is added Successfully');
    }
}
