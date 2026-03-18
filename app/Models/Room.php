<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public function rRoomPhoto()
    {
        // a room has many photos (one to many)
       return $this->hasMany(RoomPhoto::class);
    }
}
