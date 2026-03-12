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
}
