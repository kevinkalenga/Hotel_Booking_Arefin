<?php

namespace App\Http\Controllers\Front;
use App\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    public function index()
    {
          $page_data = Page::where('id', 1)->first(); 
          return view('front.privacy', compact('page_data'));

       
    }
}
