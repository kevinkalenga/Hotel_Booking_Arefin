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

    public function add()
    {
        $all_amenities = Amenity::get();
        return view('admin.room_add', compact("all_amenities"));
    }

    public function store(Request $request)
    {
        // dd($request->arr_amenities) and i will store in db 1,5,6 for example
          $amenities = '';
           $i=0;
          // if there is any value in the array   
           if(isset($request->arr_amenities)) {
              foreach($request->arr_amenities as $item) {
                if($i == 0) {
                    // zero at the first time or in the begining
                    $amenities .= $item;
                } else {
                    $amenities .= ','.$item;
                }
                $i++;
              }
           }



        $request->validate([
            'featured_photo' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'total_rooms' => 'required',
          
        ]);

         $final_name = null;

        /*
           {{old('description')}}
        */ 
        //

             if ($request->hasFile('featured_photo')) {
                $ext = $request->file('featured_photo')->extension();
                $finale_name = time().'.'.$ext;
                $request->file('featured_photo')->move(public_path('uploads/'), $finale_name);

                $obj = new Room();
                $obj->featured_photo = $finale_name;
                $obj->name = $request->name;
                $obj->description = $request->description;
                $obj->price = $request->price;
                $obj->total_rooms = $request->total_rooms;
                $obj->amenities = $amenities;
                $obj->size = $request->size;
                $obj->total_beds = $request->total_beds;
                $obj->total_bathrooms = $request->total_bathrooms;
                $obj->total_balconies = $request->total_balconies;
                $obj->total_guests = $request->total_guests;
                $obj->video_id = $request->video_id;
                
                $obj->save();
            } else {
                return back()->withErrors(['featured_photo' => 'No file uploaded']);
            }

        return redirect()->back()->with('success', 'Room is added Successfully');
    }

}
