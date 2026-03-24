<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Order;
use App\Models\Room;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Websitemail;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class BookingController extends Controller
{
    // ==================== CART ====================
    // Ajouter une chambre au panier
    public function cart_submit(Request $request)
    {
         // Validation des données envoyées
        $request->validate([
            'room_id' => 'required|integer',
            'checkin_checkout' => 'required',
            'adult' => 'required|integer|min:1',
        ]);
         
         // Séparation des dates (format: "dd/mm/yyyy - dd/mm/yyyy")
        $dates = explode(' - ', $request->checkin_checkout);
        
        // Vérification du format
        if (count($dates) != 2) {
            return redirect()->back()->with('error', 'Invalid date range format.');
        }
         
         // Conversion en objets Carbon
        $checkin = Carbon::createFromFormat('d/m/Y', trim($dates[0]));
        $checkout = Carbon::createFromFormat('d/m/Y', trim($dates[1]));
        
        // Vérification logique des dates
        if ($checkout->lessThanOrEqualTo($checkin)) {
            return redirect()->back()->with('error', 'Checkout must be after checkin');
        }
        
        // Formatage pour stockage
        $checkin_date = $checkin->format('Y-m-d');
        $checkout_date = $checkout->format('Y-m-d');
        
        // Stockage dans la session (panier)
        session()->push('cart_room_id', $request->room_id);
        session()->push('cart_checkin_date', $checkin_date);
        session()->push('cart_checkout_date', $checkout_date);
        session()->push('cart_adult', $request->adult);
        session()->push('cart_children', $request->children);

        return redirect()->back()->with('success', 'Room added to cart successfully!');
    }

    // Affichage du panier
    public function cart_view()
    {
        return view('front.cart');
    }

    // Supprimer une chambre du panier
    public function cart_delete($id)
    {
        // Récupération des données du panier
        $arr_cart_room_id = session()->get('cart_room_id', []);
        $arr_cart_checkin_date = session()->get('cart_checkin_date', []);
        $arr_cart_checkout_date = session()->get('cart_checkout_date', []);
        $arr_cart_adult = session()->get('cart_adult', []);
        $arr_cart_children = session()->get('cart_children', []);
        
        // Vérification si panier vide
        if (empty($arr_cart_room_id)) {
            return redirect()->back()->with('error', 'No items found in cart.');
        }

        // Vide le panier pour reconstruire sans l'élément supprimé
        session()->forget([
            'cart_room_id',
            'cart_checkin_date',
            'cart_checkout_date',
            'cart_adult',
            'cart_children'
        ]);
         
        // Reconstruction sans l'élément supprimé
        for ($i = 0; $i < count($arr_cart_room_id); $i++) {
            if ($arr_cart_room_id[$i] != $id) {
                session()->push('cart_room_id', $arr_cart_room_id[$i]);
                session()->push('cart_checkin_date', $arr_cart_checkin_date[$i]);
                session()->push('cart_checkout_date', $arr_cart_checkout_date[$i]);
                session()->push('cart_adult', $arr_cart_adult[$i]);
                session()->push('cart_children', $arr_cart_children[$i]);
            }
        }

        return redirect()->back()->with('success', 'Cart item deleted successfully');
    }

    // ==================== CHECKOUT ====================
    public function checkout()
    {
        // Vérifie si le client est connecté
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('cart')->with('error', 'You must login in order to checkout');
        }
        
        // Vérifie si le panier est vide
        if (!session()->has('cart_room_id') || empty(session('cart_room_id'))) {
            return redirect()->route('cart')->with('error', 'There is no item in the cart');
        }
        
        // Calcul du total
        $total_price = $this->calculateTotal();
        
        // Vérifie montant minimum
        if ($total_price < 0.50) {
            return redirect()->back()->with('error', 'Minimum payment is $0.50');
        }
        
        // Récupération du panier
        $cart_room_id = session()->get('cart_room_id', []);
        $cart_checkin_date = session()->get('cart_checkin_date', []);
        $cart_checkout_date = session()->get('cart_checkout_date', []);
        
        // Vérification disponibilité (anti double réservation)
        foreach ($cart_room_id as $i => $room_id) {
            $start = Carbon::parse($cart_checkin_date[$i]);
            $end = Carbon::parse($cart_checkout_date[$i]);
             // Boucle jour par jour pour bloquer les dates
            while ($start->lt($end)) {
                $exists = DB::table('booked_rooms')
                    ->where('room_id', $room_id)
                    ->where('booking_date', $start->format('Y-m-d'))
                    ->exists();

                if ($exists) {
                    return redirect()->route('cart')
                        ->with('error', 'Room not available on ' . $start->format('d/m/Y'));
                }

                $start->addDay();
            }
        }

        return view('front.checkout', compact('total_price'));
    }

    // ==================== PAYMENT ====================
    public function payment(Request $request)
    {
        // Vérifie login
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('cart')->with('error', 'You must login in order to checkout');
        }
        
         // Vérifie panier
        if (!session()->has('cart_room_id') || empty(session('cart_room_id'))) {
            return redirect()->route('cart')->with('error', 'There is no item in the cart');
        }
        
         // Validation des infos de facturation
        $request->validate([
            'billing_name' => 'required',
            'billing_email' => 'required|email',
            'billing_phone' => 'required',
            'billing_country' => 'required',
            'billing_address' => 'required',
            'billing_state' => 'required',
            'billing_city' => 'required',
            'billing_zip' => 'required',
        ]);

        // Sauvegarde en session
        session([
            'billing_name' => $request->billing_name,
            'billing_email' => $request->billing_email,
            'billing_phone' => $request->billing_phone,
            'billing_country' => $request->billing_country,
            'billing_address' => $request->billing_address,
            'billing_state' => $request->billing_state,
            'billing_city' => $request->billing_city,
            'billing_zip' => $request->billing_zip,
        ]);
        
         // Calcul total
        $total_price = $this->calculateTotal();
        session(['total_price' => $total_price]);

        return view('front.payment');
    }
    
    // Paiement PayPal (préparation)
    public function paypal()
    {
        if (!session()->has('cart_room_id')) {
            return redirect()->route('cart')->with('error', 'Cart is empty');
        }

        $total_price = $this->calculateTotal();
        session(['total_price' => $total_price]);

        return view('front.payment');
    }

  


    public function paymentSuccess(Request $request)
    {
         // Vérifie transaction
        if (!$request->transaction_id) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction ID missing'
            ]);
        }

        $customer_id = Auth::guard('customer')->id();
        // Montant payé
        $paid_amount = floatval($request->paid_amount ?? session()->get('total_price', 0));

        if ($paid_amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid paid amount'
            ]);
        }

        DB::beginTransaction();

        try {

            // Création de la commande
            $order = Order::create([
                'customer_id' => $customer_id,
                'order_no' => 'ORD-' . strtoupper(Str::random(8)),
                'transaction_id' => $request->transaction_id ?? null,
                'payment_method' => 'PayPal',
                'paid_amount' => $paid_amount,
                'booking_date' => now(),
                'status' => 'Completed',
            ]);

            //  Sauvegarde détails + booked_rooms
            $this->saveOrderDetails($order);

            //  Tout est OK → commit
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment and order saved successfully!',
                'redirect' => route('customer_home')
            ]);

        } catch (\Exception $e) {

            // Annulation en cas d'erreur
            DB::rollBack();

            return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
     
    // Paiement annulé
    public function paymentCancel()
    {
        return redirect()->route('cart')->with('error', 'Payment cancelled');
    }

    // ==================== STRIPE ====================
    public function stripeCreateIntent(Request $request)
    {
        $total_price = $this->calculateTotal();
        session(['total_price' => $total_price]);

        if ($total_price < 0.50) {
            return response()->json(['success' => false, 'message' => 'Minimum amount is $0.50']);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $intent = PaymentIntent::create([
                'amount' => intval($total_price * 100),
                'currency' => 'usd',
                'metadata' => ['customer_id' => Auth::guard('customer')->id() ?? 0],
            ]);

            return response()->json(['success' => true, 'clientSecret' => $intent->client_secret]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Stripe error: ' . $e->getMessage()]);
        }
    }

   

    // Succès paiement Stripe
    public function stripeSuccess(Request $request)
    {
        $transaction_id = $request->transaction_id;

        if (!$transaction_id) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction ID missing.'
            ]);
        }

        Stripe::setApiKey(config('services.stripe.secret'));
    
        try {
             // Récupération du paiement Stripe
            $intent = PaymentIntent::retrieve($transaction_id);
            $paid_amount = ($intent->amount ?? 0) / 100;

            if ($paid_amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid paid amount.'
                ]);
            }

            $customer_id = Auth::guard('customer')->id();

            DB::beginTransaction();

            // Création de la commande
            $order = Order::create([
                'customer_id' => $customer_id,
                'order_no' => 'ORD-' . strtoupper(Str::random(8)),
                'transaction_id' => $transaction_id,
                'payment_method' => 'Stripe',
                'paid_amount' => $paid_amount,
                'booking_date' => now(),
                'status' => 'Completed',
            ]);

            // Sauvegarde détails + booked_rooms
            $this->saveOrderDetails($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment successful and order saved!',
                'redirect' => route('customer_home')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            $message = $e->getMessage();

            // Friendly message si chambre indisponible
            if (str_contains($message, 'Room already booked')) {
              $message = 'Sorry, one or more rooms in your order are no longer available. Please review your cart.';
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        }
    }

    // ==================== UTILS ====================

        // Calcul du prix total du panier
    private function calculateTotal()
    {
        $total = 0;
        $cart_room_id = session()->get('cart_room_id', []);
        $cart_checkin_date = session()->get('cart_checkin_date', []);
        $cart_checkout_date = session()->get('cart_checkout_date', []);

        foreach ($cart_room_id as $i => $room_id) {
            $room = Room::find($room_id);
            if (!$room) continue;

            $checkin = Carbon::parse($cart_checkin_date[$i]);
            $checkout = Carbon::parse($cart_checkout_date[$i]);
            // Calcul nombre de nuits
            $nights = max(1, $checkin->diffInDays($checkout));
             // Ajout au total
            $total += $room->price * $nights;
        }

        return $total;
    }
    
    // Sauvegarde des détails de commande + réservation des dates
    private function saveOrderDetails($order)
    {
        $cart_room_id = session()->get('cart_room_id', []);
        $cart_checkin_date = session()->get('cart_checkin_date', []);
        $cart_checkout_date = session()->get('cart_checkout_date', []);
        $cart_adult = session()->get('cart_adult', []);
        $cart_children = session()->get('cart_children', []);

        foreach ($cart_room_id as $i => $room_id) {
            $room = Room::find($room_id);
            if (!$room) continue;

            $checkin = Carbon::parse($cart_checkin_date[$i]);
            $checkout = Carbon::parse($cart_checkout_date[$i]);
            $nights = max(1, $checkin->diffInDays($checkout));
            $subtotal = $room->price * $nights;
            
            // Sauvegarde du détail de commande
            OrderDetail::create([
                'order_id' => $order->id,
                'room_id' => $room_id,
                'checkin_date' => $checkin->format('Y-m-d'),
                'checkout_date' => $checkout->format('Y-m-d'),
                'adult' => $cart_adult[$i] ?? 1,
                'children' => $cart_children[$i] ?? 0,
                'subtotal' => $subtotal,
            ]);
             
            // Gestion des dates (UNE PAR JOUR)
            $start = $checkin->copy();
            // Boucle jour par jour pour bloquer les dates
            while ($start->lt($checkout)) {
                
                $date = $start->format('Y-m-d');

                 // Vérification anti double réservation
                $exists = DB::table('booked_rooms')
                    ->where('room_id', $room_id)
                    ->where('booking_date', $date)
                    ->exists();

                if ($exists) {
                    throw new \Exception('Room already booked for date: ' . $date);
                }
            
                // Insertion dans booked_rooms
                DB::table('booked_rooms')->insert([
                    'room_id' => $room_id,
                    'order_no' => $order->order_no,
                    'booking_date' => $start->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $start->addDay();
            }
        }

        // vider le panier
        session()->forget(['cart_room_id', 'cart_checkin_date', 'cart_checkout_date', 'cart_adult', 'cart_children', 'total_price']);
    }
     
     // Version Stripe alternative (non utilisée ici)
    private function saveOrderDetailsStripe($transaction_id, $paid_amount)
    {
        $customer_id = Auth::guard('customer')->id();
        $order = Order::create([
            'customer_id' => $customer_id,
            'order_no' => 'ORD-' . strtoupper(Str::random(8)),
            'transaction_id' => $transaction_id,
            'payment_method' => 'Stripe',
            'paid_amount' => $paid_amount,
            'booking_date' => now(),
            'status' => 'Completed',
        ]);

        $this->saveOrderDetails($order);
    }
}