<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Amenity;
use App\Models\RoomPhoto;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::get();
        return view('admin.room_view', compact('rooms'));
    }
}
