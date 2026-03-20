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
}
