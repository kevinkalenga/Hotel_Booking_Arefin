<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Order;
use App\Models\Room;
use Illuminate\Support\Str;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Mail;
use App\Mail\Websitemail;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ==================== CART ====================
    // Ajouter une chambre au panier
  
    public function cart_submit(Request $request)
    {
       $request->validate([
           'room_id' => 'required|integer',
           'checkin_checkout' => 'required',
           'adult' => 'required|integer|min:1',
       ]);

       $dates = explode(' - ', $request->checkin_checkout);

       if (count($dates) != 2) {
          return redirect()->back()->with('error', 'Invalid date range format.');
        }

        //  SAFE parsing
        $checkin = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]));
        $checkout = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]));

        if ($checkout->lessThanOrEqualTo($checkin)) {
          return redirect()->back()->with('error', 'Checkout must be after checkin');
        }

        $checkin_date = $checkin->format('Y-m-d');
        $checkout_date = $checkout->format('Y-m-d');

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
        $arr_cart_room_id = session()->get('cart_room_id', []);
        $arr_cart_checkin_date = session()->get('cart_checkin_date', []);
        $arr_cart_checkout_date = session()->get('cart_checkout_date', []);
        $arr_cart_adult = session()->get('cart_adult', []);
        $arr_cart_children = session()->get('cart_children', []);

        if (empty($arr_cart_room_id)) {
            return redirect()->back()->with('error', 'No items found in cart.');
        }

        // Vide le panier pour reconstruire sans l'élément supprimé
        session()->forget([
            'cart_room_id', 'cart_checkin_date', 'cart_checkout_date',
            'cart_adult', 'cart_children'
        ]);

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
       $total_price = $this->calculateTotal();
      
        
        if ($total_price < 0.50) {
                 return redirect()->back()->with('error', 'Minimum payment is $0.50');
        }
        // Vérifie si le client est connecté
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('cart')->with('error', 'You must login in order to checkout');
        }

        // Vérifie que le panier n'est pas vide
        if (!session()->has('cart_room_id') || empty(session('cart_room_id'))) {
            return redirect()->route('cart')->with('error', 'There is no item in the cart');
        }

        return view('front.checkout',  compact('total_price'));
    }


    // ==================== PAYMENT PAGE ====================
    public function payment(Request $request)
    {
        // Vérifie connexion
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('cart')->with('error', 'You must login in order to checkout');
        }

        // Vérifie panier
        if (!session()->has('cart_room_id') || empty(session('cart_room_id'))) {
            return redirect()->route('cart')->with('error', 'There is no item in the cart');
        }

        // Valide les informations de facturation
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

        // Enregistre les informations en session pour le sauvegarde
        session()->put('billing_name', $request->billing_name);
        session()->put('billing_email', $request->billing_email);
        session()->put('billing_phone', $request->billing_phone);
        session()->put('billing_country', $request->billing_country);
        session()->put('billing_address', $request->billing_address);
        session()->put('billing_state', $request->billing_state);
        session()->put('billing_city', $request->billing_city);
        session()->put('billing_zip', $request->billing_zip);

       
        // Ajout de session
          $total_price = 0;
          $cart_room_id = session()->get('cart_room_id', []);
          $cart_checkin_date = session()->get('cart_checkin_date', []);
          $cart_checkout_date = session()->get('cart_checkout_date', []);

       foreach($cart_room_id as $i => $room_id){
            $room = Room::find($room_id);
            if(!$room) continue;

             $checkin = strtotime($cart_checkin_date[$i]);
             $checkout = strtotime($cart_checkout_date[$i]);
             $nights = max(1, ($checkout - $checkin)/86400);

              $total_price += $room->price * $nights;
         }

        session(['total_price' => $total_price]);
        
        
        
        return view('front.payment');
    }

    /****************************** Payment Method Paypal *************************** */
    public function paypal()
    {
         // Vérifier panier
         if (!session()->has('cart_room_id')) {
             return redirect()->route('cart')->with('error', 'Cart is empty');
         }
         
         // Calcul du total réel du panier
         $total_price = 0;
         $cart_room_id = session()->get('cart_room_id', []);
         $cart_checkin_date = session()->get('cart_checkin_date', []);
         $cart_checkout_date = session()->get('cart_checkout_date', []);

        foreach($cart_room_id as $i => $room_id){
             $room = Room::find($room_id);
             if(!$room) continue;

             $checkin = strtotime($cart_checkin_date[$i]);
             $checkout = strtotime($cart_checkout_date[$i]);
             $nights = max(1, ($checkout - $checkin)/86400);

             $total_price += $room->price * $nights;
        }

        // Stocker le total réel dans la session pour PayPal
        session()->put('total_price', $total_price);

        return view('front.payment');
    }




    // ==================== PAYMENT SUCCESS ====================
   
    public function paymentSuccess(Request $request)
    {
        if(!$request->transaction_id){
              return response()->json([
                  'success' => false,
                  'message' => 'Transaction ID missing'
              ]);
        }
    
      // Client connecté
       $customer_id = Auth::guard('customer')->id();
        // Montant payé
        $paid_amount = floatval($request->paid_amount ?? session()->get('total_price', 0));

            if($paid_amount <= 0){
              return response()->json([
                  'success' => false,
                  'message' => 'Invalid paid amount'
              ]);
            }
        // 1 Créer la commande
       $order = Order::create([
        'customer_id' => $customer_id,
        'order_no' => 'ORD-' . strtoupper(Str::random(8)),
        'transaction_id' => $request->transaction_id ?? null,
        'payment_method' => 'PayPal',
        'paid_amount' => $paid_amount,
        'booking_date' => now(),
        'status' => 'Completed',
       ]);

        // 2️ Récupérer panier
        $cart_room_id = session()->get('cart_room_id', []);
        $cart_checkin_date = session()->get('cart_checkin_date', []);
        $cart_checkout_date = session()->get('cart_checkout_date', []);
        $cart_adult = session()->get('cart_adult', []);
        $cart_children = session()->get('cart_children', []);

       
        // 3️ Enregistrer chaque chambre
           foreach($cart_room_id as $i => $room_id){

             $room = Room::find($room_id);
             if(!$room) continue;

             //  convertir d'abord les dates
               $checkin = strtotime($cart_checkin_date[$i]);
               $checkout = strtotime($cart_checkout_date[$i]);

             //  ensuite calcul
             $nights = max(1, ($checkout - $checkin) / 86400);

            $subtotal = $room->price * $nights;

             OrderDetail::create([
                 'order_id' => $order->id,
                 'room_id' => $room_id,
                 'checkin_date' => date('Y-m-d', $checkin),
                 'checkout_date' => date('Y-m-d', $checkout),
                 'adult' => $cart_adult[$i] ?? 1,
                 'children' => $cart_children[$i] ?? 0,
                 'subtotal' => $subtotal,
             ]);
           }


           // 4 Envoyer un email au client
        $subject = 'Booking Confirmation - ' . $order->order_no;

        $body = "Hi " . (session('billing_name') ?? 'Customer') . ",<br><br>";
        $body .= "Thank you for your booking. Here are your order details:<br>";
        $body .= "<ul>";
        $body .= "<li>Order No: {$order->order_no}</li>";
        $body .= "<li>Payment Method: {$order->payment_method}</li>";
        $body .= "<li>Paid Amount: $" . number_format($order->paid_amount, 2) . "</li>";
        $body .= "<li>Booking Date: " . $order->booking_date->format('d/m/Y') . "</li>";
        $body .= "</ul><br>";
        $body .= "We look forward to welcoming you!";

        if(session('billing_email')){
            Mail::to(session('billing_email'))->send(new Websitemail($subject, $body));
        }
       
       
       
        //5 Vider panier
        session()->forget([
        'cart_room_id',
        'cart_checkin_date',
        'cart_checkout_date',
        'cart_adult',
        'cart_children',
        'total_price',
      ]);

    // Force Laravel à renvoyer JSON correct
      return response()->json([
        'success' => true,
        'message' => 'Payment and order saved successfully!',
        'redirect' => route('customer_home')
       
      ]);
    }
    

    public function paymentCancel()
    {
      return redirect()->route('cart')->with('error', 'Payment cancelled');
    }


   
      /****************************** Payment Method Stripe *************************** */

    

      // Crée le PaymentIntent Stripe
       public function stripeCreateIntent(Request $request)
{
    \Log::info('Stripe CreateIntent called'); // trace pour debug
    $total_price = $this->calculateTotal();
    session(['total_price' => $total_price]);
    \Log::info('Total price from session: ' . $total_price);
     
       if ($total_price < 0.50) {
             return response()->json([
                'success' => false,
                'message' => 'Minimum amount is $0.50'
            ]);
        }

    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $intent = \Stripe\PaymentIntent::create([
            'amount' => intval($total_price * 100),
            'currency' => 'usd',
            'metadata' => [
                'customer_id' => Auth::guard('customer')->id() ?? 0,
            ],
        ]);

        \Log::info('PaymentIntent created: ' . $intent->id);

        return response()->json([
            'success' => true,
            'clientSecret' => $intent->client_secret,
        ]);
    } catch (\Exception $e) {
        \Log::error('Stripe CreateIntent Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Stripe error: ' . $e->getMessage()
        ]);
    }
}

    
    // Payment Stripe réussi
    public function stripeSuccess(Request $request)
    {
        $transaction_id = $request->transaction_id;
        if(!$transaction_id){
            return response()->json([
                'success' => false,
                'message' => 'Transaction ID missing.'
            ]);
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $intent = \Stripe\PaymentIntent::retrieve($transaction_id);

        $paid_amount = $intent->amount / 100;

        $customer_id = Auth::guard('customer')->id();
        $cart_room_id = session()->get('cart_room_id', []);
        $cart_checkin_date = session()->get('cart_checkin_date', []);
        $cart_checkout_date = session()->get('cart_checkout_date', []);
        $cart_adult = session()->get('cart_adult', []);
        $cart_children = session()->get('cart_children', []);
        
   

        if(empty($cart_room_id)){
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty.'
            ]);
        }

        // 1️⃣ Créer l'ordre
        $order = Order::create([
            'customer_id' => $customer_id,
            'order_no' => 'ORD-' . strtoupper(Str::random(8)),
            'transaction_id' => $transaction_id,
            'payment_method' => 'Stripe',
            'paid_amount' => $paid_amount,
            'booking_date' => now(),
            'status' => 'Completed',
        ]);

        // 2️⃣ Enregistrer chaque chambre
        foreach($cart_room_id as $i => $room_id){
            $room = Room::find($room_id);
            if(!$room) continue;

             $checkin = $cart_checkin_date[$i]; // déjà Y-m-d
             $checkout = $cart_checkout_date[$i]; // déjà Y-m-d

            $nights = max(1, (strtotime($checkout) - strtotime($checkin))/86400);
            $subtotal = $room->price * $nights;

            OrderDetail::create([
                'order_id' => $order->id,
                'room_id' => $room_id,
                'checkin_date' => $checkin,
                'checkout_date' => $checkout,
                'adult' => $cart_adult[$i] ?? 1,
                'children' => $cart_children[$i] ?? 0,
                'subtotal' => $subtotal,
            ]);
        }

        // 3️⃣ Envoyer email au client
        $customer_email = session()->get('billing_email') ?? Auth::guard('customer')->user()->email ?? null;
        if($customer_email){
            $subject = 'Booking Confirmed - ' . $order->order_no;
            $body = "Hi " . (session('billing_name') ?? 'Customer') . ",<br><br>";
            $body .= "Thank you for your booking. Here are your order details:<br>";
            $body .= "<ul>";
            $body .= "<li>Order No: {$order->order_no}</li>";
            $body .= "<li>Payment Method: {$order->payment_method}</li>";
            $body .= "<li>Paid Amount: $" . number_format($order->paid_amount, 2) . "</li>";
            $body .= "<li>Booking Date: " . $order->booking_date->format('d/m/Y') . "</li>";
            $body .= "</ul><br>";
            $body .= "We look forward to welcoming you!";

            Mail::to($customer_email)->send(new Websitemail($subject, $body));
        }

        // 4️⃣ Vider panier
        session()->forget(['cart_room_id','cart_checkin_date','cart_checkout_date','cart_adult','cart_children','total_price']);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful and order saved!',
            'redirect' => route('customer_home')
        ]);
    }


    private function calculateTotal()
   {
    $total = 0;

    $cart_room_id = session()->get('cart_room_id', []);
    $cart_checkin_date = session()->get('cart_checkin_date', []);
    $cart_checkout_date = session()->get('cart_checkout_date', []);

    foreach($cart_room_id as $i => $room_id){
        $room = Room::find($room_id);
        if(!$room) continue;

        $checkin = strtotime($cart_checkin_date[$i]);
        $checkout = strtotime($cart_checkout_date[$i]);

        $nights = max(1, ($checkout - $checkin)/86400);
        $total += $room->price * $nights;
    }

    return $total;
   }
}
    
    
    
    
    
    



