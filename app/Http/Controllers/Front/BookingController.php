<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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


      // 43 Ajout au panier (session variable array à gauche qui contient la valeur à droite)
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
}
