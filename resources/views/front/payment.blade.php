@extends('front.layout.app')

@section('main_content')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=USD"></script>
<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $global_page_data->payment_heading }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="container">
        <div class="row">
                  {{-- Payment Method --}}
            <div class="col-lg-4 col-md-4 checkout-left mb_30">
                        
                        <h4>Make Payment</h4>
                        <select name="payment_method" class="form-control select2" id="paymentMethodChange" autocomplete="off">
                            <option value="">Select Payment Method</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Stripe">Stripe</option>
                        </select>

                        <div class="paypal mt_20">
                            <h4>Pay with PayPal</h4>
                            <div id="paypal-button-container"></div>
                        </div>
    
                        <div class="stripe mt_20">
                            <h4>Pay with Stripe</h4>
                            <div id="card-element" style="padding:10px; border:1px solid #ccc; border-radius:5px;"></div>
                             <button style="margin-top:10px;" id="stripe-button">Pay With Stripe</button>
                              <div id="card-errors" role="alert" style="color:red; margin-top:10px;"></div>
                        </div>
    
            </div>

            {{-- Billing details --}}
            <div class="col-lg-4 col-md-4 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Billing Details</h4>
                    <div>Name: {{ session()->get('billing_name') }}</div>
                    <div>Email: {{ session()->get('billing_email') }}</div>
                    <div>Phone: {{ session()->get('billing_phone') }}</div>
                    <div>Country: {{ session()->get('billing_country') }}</div>
                    <div>Address: {{ session()->get('billing_address') }}</div>
                    <div>State: {{ session()->get('billing_state') }}</div>
                    <div>City: {{ session()->get('billing_city') }}</div>
                    <div>Zip: {{ session()->get('billing_zip') }}</div>
                </div>
            </div>

            {{-- Cart Summary + Payment --}}
            <div class="col-lg-4 col-md-4 checkout-right">
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

                   
                </div>
            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {

    paypal.Buttons({

        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '{{ session('total_price') ?? 100 }}'
                    }
                }]
            });
        },

        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {

                // Important : retourner fetch pour que PayPal JS sache que c'est fini
                return fetch("{{ route('payment.success') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        transaction_id: details.id,
                        paid_amount: '{{ session('total_price') ?? 100 }}'
                    })
                })
                .then(res => res.json())
                .then(response => {
                    if(response.success) {
                        window.location.href = "{{ route('customer_home') }}";
                    } else {
                        alert("Payment was successful but saving order failed.");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Payment succeeded but something went wrong.");
                });

            });
        },

        onCancel: function (data) {
            window.location.href = "{{ route('payment.cancel') }}";
        }

    }).render('#paypal-button-container');

});
</script>

   <script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    const card = elements.create('card', {hidePostalCode: true});
    card.mount('#card-element');

    const stripeBtn = document.getElementById('stripe-button');
    const cardErrors = document.getElementById('card-errors');

    stripeBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        cardErrors.textContent = '';
        stripeBtn.disabled = true;

        // 1️⃣ Crée le PaymentIntent
        let res, data;
        try {
            res = await fetch('{{ route('stripe.createIntent') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });
            data = await res.json();
        } catch(err) {
            cardErrors.textContent = 'Error creating payment intent.';
            stripeBtn.disabled = false;
            return;
        }

        if(!data.success){
            cardErrors.textContent = data.message || 'Failed to create payment intent.';
            stripeBtn.disabled = false;
            return;
        }

        // 2️⃣ Confirme le paiement
        const {error, paymentIntent} = await stripe.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: card,
                billing_details: {
                    name: "{{ session('billing_name') }}",
                    email: "{{ session('billing_email') }}"
                }
            }
        });

        if(error){
            cardErrors.textContent = error.message;
            stripeBtn.disabled = false;
            return;
        }

        if(paymentIntent.status === 'succeeded'){
            // 3️⃣ Notifie Laravel
            let notifyResponse;
            try {
                const notify = await fetch('{{ route('stripe.success') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ transaction_id: paymentIntent.id })
                });
                notifyResponse = await notify.json();
            } catch(err) {
                cardErrors.textContent = 'Payment succeeded but saving order failed.';
                return;
            }

            if(notifyResponse.success){
                window.location.href = notifyResponse.redirect;
            } else {
                cardErrors.textContent = 'Payment succeeded but saving order failed.';
            }
        }
    });
});
</script>
@endsection
