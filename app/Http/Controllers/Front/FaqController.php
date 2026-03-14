<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
          $faq_all = Faq::get(); //Limiter le nombre de postes
          return view('front.faq', compact('faq_all'));
    }
}
