<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
     protected $fillable = [
        'order_id',
        'room_id',
        'checkin_date',
        'checkout_date',
        'adult',
        'children',
        'subtotal'
    ];
}
