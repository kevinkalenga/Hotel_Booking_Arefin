<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Customer;
use Hash;

class AdminBookingController extends Controller
{
    /**
     * Show the create booking form
     */
    public function create()
    {
        $rooms = Room::all();
        return view('admin.booking.create', compact('rooms'));
    }

    /**
     * Store a manual booking
     */
    // public function store(Request $request)
    // {
    //     // Validation
    //     $request->validate([
    //         'customer_id' => 'nullable|exists:customers,id',
    //         'room_id' => 'required|exists:rooms,id',
    //         'checkin' => 'required|date',
    //         'checkout' => 'required|date|after:checkin',
    //         'adult' => 'required|integer|min:1',
    //         'payment_status' => 'required|in:pending,paid',
    //         'paid_amount' => 'nullable|numeric|min:0',
    //         'children' => 'nullable|integer|min:0'
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         // Déterminer le status
    //         $status = $request->payment_status === 'paid' ? 'Completed' : 'Pending';

    //         $customer_id = $request->customer_id;

    //         if (!$customer_id) {
    //             $customer = Customer::create([
    //                 'name' => $request->name,
    //                 'email' => $request->email,
    //                 'phone' => $request->phone,
    //                 'password' => Hash::make(Str::random(8)),
    //             ]);

    //             $customer_id = $customer->id;
    //         }

    //         // Créer la commande
    //         $order = Order::create([
    //             'customer_id' => $customer_id, // nullable
    //             'order_no' => 'ORD-' . strtoupper(Str::random(8)),
    //             'transaction_id' => null,
    //             'payment_method' => 'Manual',
    //             'paid_amount' => $request->paid_amount ?? 0,
    //             'booking_date' => now(),
    //             'status' => $status,
    //         ]);

    //         // Sauvegarder les détails et vérifier le surbooking
    //         $this->saveBookingDetails($order, $request);

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Booking created successfully. Status: ' . $order->status);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }

      
      public function store(Request $request)
{
    
    // Validation
    $request->validate([
        'customer_id' => 'nullable|exists:customers,id',
        'name' => 'required_if:customer_id,null|string',
        'email' => 'required_if:customer_id,null|email',
        'phone' => 'nullable|string',
        'room_id' => 'required|exists:rooms,id',
        'checkin' => 'required|date',
        'checkout' => 'required|date|after:checkin',
        'adult' => 'required|integer|min:1',
        'payment_status' => 'required|in:pending,paid',
        'paid_amount' => 'nullable|numeric|min:0',
        'children' => 'nullable|integer|min:0',
    ]);

    DB::beginTransaction();

    try {
        // Déterminer le status
        $status = $request->payment_status === 'paid' ? 'Completed' : 'Pending';

        // Si client existant
        $customer_id = $request->customer_id;

        // Sinon créer nouveau client
        if (!$customer_id) {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make(Str::random(8)), // mot de passe temporaire
            ]);

            $customer_id = $customer->id;
        }

        // Créer la commande
        $order = Order::create([
            'customer_id' => $customer_id,
            'order_no' => 'ORD-' . strtoupper(Str::random(8)),
            'transaction_id' => null,
            'payment_method' => 'Manual',
            'paid_amount' => $request->paid_amount ?? 0,
            'booking_date' => now(),
            'status' => $request->payment_status === 'paid' ? 'Completed' : 'Pending',
        ]);

        // Sauvegarder les détails et dates
        $this->saveBookingDetails($order, $request);

        DB::commit();

        return redirect()->back()->with('success', 'Booking created successfully. Status: ' . $order->status);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}

    /**
     * Save booking details and check availability
     */
    private function saveBookingDetails(Order $order, Request $request)
    {
        $room = Room::find($request->room_id);
        if (!$room) throw new \Exception('Room not found');

        $checkin = Carbon::parse($request->checkin);
        $checkout = Carbon::parse($request->checkout);
        $nights = max(1, $checkin->diffInDays($checkout));
        $subtotal = $room->price * $nights;

        // Créer le détail de la commande
        OrderDetail::create([
            'order_id' => $order->id,
            'room_id' => $room->id,
            'checkin_date' => $checkin->format('Y-m-d'),
            'checkout_date' => $checkout->format('Y-m-d'),
            'adult' => $request->adult,
            'children' => $request->children ?? 0,
            'subtotal' => $subtotal,
        ]);

        // Vérification anti-surbooking
        $current = $checkin->copy();
        while ($current->lt($checkout)) {
            $date = $current->format('Y-m-d');

            $exists = DB::table('booked_rooms')
                ->where('room_id', $room->id)
                ->where('booking_date', $date)
                ->exists();

            if ($exists) {
                throw new \Exception('Room already booked for date: ' . $date);
            }

            DB::table('booked_rooms')->insert([
                'room_id' => $room->id,
                'order_no' => $order->order_no,
                'booking_date' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $current->addDay();
        }
    }

    /**
     * Mark a booking as paid/completed (after payment)
     */
    public function markAsPaid(Request $request)
    {
        $request->validate([
            'order_no' => 'required|exists:orders,order_no',
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string'
        ]);

        $order = Order::where('order_no', $request->order_no)->firstOrFail();
        $order->update([
            'status' => 'Completed',
            'transaction_id' => $request->transaction_id,
            'paid_amount' => $request->amount,
            'payment_method' => $request->payment_method
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking marked as completed.'
        ]);
    }
}