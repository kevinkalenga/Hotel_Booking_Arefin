<?php

namespace App\Http\Controllers\Admin;
use App\Models\Amenity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::get();
        return view('admin.amenity_view', compact('amenities'));
    }
}
