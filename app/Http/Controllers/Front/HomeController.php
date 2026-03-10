<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class HomeController extends Controller
{
    public function index()
    {
        $slide_all = Slider::get();
        return view('front.home', compact('slide_all'));
    }
}
