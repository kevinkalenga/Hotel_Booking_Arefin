<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;

class CustomerOrderController extends Controller
{
    public function index()
    {
        // logged in customer
        $orders = Order::where('customer_id', Auth::guard('customer')->user()->id)->get();
        return view('customer.orders', compact('orders'));
    }
}
