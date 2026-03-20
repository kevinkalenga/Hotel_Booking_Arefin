<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class BookingController extends Controller
{
    // ==================== CART ====================
    // Ajouter une chambre au panier
  
    
    public function cart_submit(Request $request)
    {
      // 1️ Validation des champs
      $request->validate([
        'room_id' => 'required|integer',
        'checkin_checkout' => 'required',
        'adult' => 'required|integer|min:1',
      ]);

       // 2️ Extraction des dates
       $dates = explode(' - ', $request->checkin_checkout);
       if (count($dates) != 2) {
          return redirect()->back()->with('error', 'Invalid date range format.');
       }

       $checkin_date = trim($dates[0]);
       $checkout_date = trim($dates[1]);
       $room_id = $request->room_id;


      // 3 Ajout au panier (session variable array à gauche qui contient la valeur à droite)
      session()->push('cart_room_id', $room_id);
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
        // Vérifie si le client est connecté
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('cart')->with('error', 'You must login in order to checkout');
        }

        // Vérifie que le panier n'est pas vide
        if (!session()->has('cart_room_id') || empty(session('cart_room_id'))) {
            return redirect()->route('cart')->with('error', 'There is no item in the cart');
        }

        return view('front.checkout');
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
             $room = DB::table('rooms')->find($room_id);
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
        $customer_id = Auth::guard('customer')->id();
        $cart_room_id = session()->get('cart_room_id', []);
        $cart_checkin_date = session()->get('cart_checkin_date', []);
        $cart_checkout_date = session()->get('cart_checkout_date', []);
        $cart_adult = session()->get('cart_adult', []);
        $cart_children = session()->get('cart_children', []);
        $total_price = session()->get('total_price', 0);

        // Enregistre chaque réservation dans la base de données
        foreach ($cart_room_id as $i => $room_id) {
            Booking::create([
                'customer_id' => $customer_id,
                'room_id' => $room_id,
                'checkin' => $cart_checkin_date[$i] ?? null,
                'checkout' => $cart_checkout_date[$i] ?? null,
                'adult' => $cart_adult[$i] ?? 1,
                'children' => $cart_children[$i] ?? 0,
                'total_price' => $total_price,
                'payment_method' => 'PayPal',
                'payment_status' => 'Paid',
                'transaction_id' => $request->query('token') ?? null, // ID PayPal si dispo
            ]);
        }

        // Vider le panier après paiement
        session()->forget([
            'cart_room_id',
            'cart_checkin_date',
            'cart_checkout_date',
            'cart_adult',
            'cart_children',
            'total_price'
        ]);

        return redirect()->route('home')->with('success', 'Payment successful!');
    }


//    public function paymentSuccess()
//    {
//     // 🔥 ici future logique DB

//     // Vider panier
//     session()->forget('cart_room_id');
//     session()->forget('cart_checkin_date');
//     session()->forget('cart_checkout_date');

//     return redirect()->route('home')->with('success', 'Payment successful!');
//    }


    public function paymentCancel()
    {
      return redirect()->route('cart')->with('error', 'Payment cancelled');
    }


//    private function calculateTotal()
//    {
//     $total = 0;

//     $rooms = session('cart_room_id', []);

//     foreach ($rooms as $key => $room_id) {
//         $room = DB::table('rooms')->find($room_id);

//         if (!$room) continue;

//         $checkin = session('cart_checkin_date')[$key];
//         $checkout = session('cart_checkout_date')[$key];

//         $nights = (strtotime($checkout) - strtotime($checkin)) / 86400;

//         $total += $room->price * $nights;
//     }

//       return $total;
//    }
}
