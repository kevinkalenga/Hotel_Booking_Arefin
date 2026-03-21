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


    /**
     * Get the customer who placed this order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all details (rooms) associated with this order.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
