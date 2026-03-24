<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use Auth;

class AdminOrderController extends Controller
{
    public function index()
    {
        // logged in customer
        $orders = Order::get();
        return view('admin.orders', compact('orders'));
    }

    public function invoice($id)
    {
         $order = Order::with('customer')->findOrFail($id);
         $order_detail = OrderDetail::where('order_id', $id)->get();
        //  $customer_data = Customer::where('id', $orders->customer_id)->first();
        return view('admin.invoice', compact('order', 'order_detail'));
    }

    public function delete($id)
    {
       $obj = Order::where('id', $id)->first();
       $obj->delete();
       
       $obj = OrderDetail::where('order_id', $id)->delete();

       return redirect()->back()->with('success', 'Order is deleted successfully');
    }


    public function markAsCompleted($id)
   {
    $order = \App\Models\Order::findOrFail($id);

    $order->status = 'Completed';
    $order->save();

    return redirect()->back()->with('success', 'Client checked-in successfully');
   }

    
}
