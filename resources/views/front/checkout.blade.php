@extends('front.layout.app')

@section('main_content')
<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $global_page_data->checkout_heading }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="container">
        <div class="row">
            {{-- Billing Info --}}
            <div class="col-lg-8 col-md-6 checkout-left">
                <form action="{{ route('payment') }}" method="post" class="form_checkout">
                    @csrf
                    <div class="billing-info">
                        <h4 class="mb_30">Billing Information</h4>
                        @php
                            // Récupération des infos du client
                            $billing_name = session()->get('billing_name', Auth::guard('customer')->user()->name);
                            $billing_email = session()->get('billing_email', Auth::guard('customer')->user()->email);
                            $billing_phone = session()->get('billing_phone', Auth::guard('customer')->user()->phone);
                            $billing_country = session()->get('billing_country', Auth::guard('customer')->user()->country);
                            $billing_address = session()->get('billing_address', Auth::guard('customer')->user()->address);
                            $billing_state = session()->get('billing_state', Auth::guard('customer')->user()->state);
                            $billing_city = session()->get('billing_city', Auth::guard('customer')->user()->city);
                            $billing_zip = session()->get('billing_zip', Auth::guard('customer')->user()->zip);
                        @endphp

                        <div class="row">
                            <div class="col-lg-6">
                                <label for="">Name: *</label>
                                <input type="text" class="form-control mb_15" name="billing_name" value="{{ $billing_name }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Email Address: *</label>
                                <input type="email" class="form-control mb_15" name="billing_email" value="{{ $billing_email }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Phone Number: *</label>
                                <input type="text" class="form-control mb_15" name="billing_phone" value="{{ $billing_phone }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Country: *</label>
                                <input type="text" class="form-control mb_15" name="billing_country" value="{{ $billing_country }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Address: *</label>
                                <input type="text" class="form-control mb_15" name="billing_address" value="{{ $billing_address }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">State: *</label>
                                <input type="text" class="form-control mb_15" name="billing_state" value="{{ $billing_state }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">City: *</label>
                                <input type="text" class="form-control mb_15" name="billing_city" value="{{ $billing_city }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="">Zip Code: *</label>
                                <input type="text" class="form-control mb_15" name="billing_zip" value="{{ $billing_zip }}">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary bg-website mb_30">Continue to payment</button>
                </form>
            </div>

            {{-- Cart Summary + Payment --}}
            <div class="col-lg-4 col-md-6 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Cart Details</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                @php
                                    $cart_room_id = session()->get('cart_room_id', []);
                                    $cart_checkin_date = session()->get('cart_checkin_date', []);
                                    $cart_checkout_date = session()->get('cart_checkout_date', []);
                                    $cart_adult = session()->get('cart_adult', []);
                                    $cart_children = session()->get('cart_children', []);

                                    $total_price = 0;
                                @endphp

                                @foreach($cart_room_id as $i => $room_id)
                                    @php
                                        $room = \App\Models\Room::find($room_id);
                                        if (!$room) continue;

                                        $checkin_str = $cart_checkin_date[$i] ?? null;
                                        $checkout_str = $cart_checkout_date[$i] ?? null;
                                        $nights = 0;
                                        $subtotal = 0;

                                        if ($checkin_str && $checkout_str) {
                                            $checkin = strtotime($checkin_str);
                                            $checkout = strtotime($checkout_str);

                                            $nights = max(1, ceil(($checkout - $checkin)/86400));

                                            $subtotal = $room->price * $nights;
                                            $total_price += $subtotal;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $room->name }}</td>
                                        <td>{{ $checkin_str }} - {{ $checkout_str }}</td>
                                        <td>Adult: {{ $cart_adult[$i] ?? 1 }}<br>Children: {{ $cart_children[$i] ?? 0 }}</td>
                                        <td>${{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td colspan="3"><b>Total:</b></td>
                                    <td><b>${{ number_format($total_price, 2) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- PayPal + Stripe Buttons --}}
                    <div class="mt_20">
                        <!-- <h4>Payment</h4> -->
                        <div id="paypal-button-container"></div>
                        <div id="stripe-button" class="mt_2"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
