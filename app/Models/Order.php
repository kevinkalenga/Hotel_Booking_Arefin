<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
     protected $fillable = [
        'customer_id',
        'order_no',
        'transaction_id',
        'payment_method',
        'card_last_digit',
        'paid_amount',
        'booking_date',
        'status',
    ];
}
