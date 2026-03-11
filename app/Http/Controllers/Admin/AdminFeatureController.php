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
}
