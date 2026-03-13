<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;

class VideoController extends Controller
{
    public function index()
    {
          $video_all = Video::paginate(4); //Limiter le nombre de postes
          return view('front.video_gallery', compact('video_all'));
    }
}
