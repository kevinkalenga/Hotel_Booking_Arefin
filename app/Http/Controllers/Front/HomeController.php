<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Feature;

class HomeController extends Controller
{
    public function index()
    {
        $slide_all = Slider::get();
        $feature_all = Feature::get();
        return view('front.home', compact('slide_all', 'feature_all'));
    }
}
