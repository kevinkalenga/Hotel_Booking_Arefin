@extends('front.layout.app')

@section('main_content')
    
<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $global_page_data->cart_heading }}</h2>
            </div>
        </div>
    </div>
</div>

{{--  Messages flash --}}
@if (session('error'))
    <script>
        iziToast.show({
            message: {!! json_encode(session('error')) !!},
            color: 'red',
            position: 'topRight',
        });
    </script>
@endif

@if (session('success'))
    <script>
        iziToast.show({
            message: {!! json_encode(session('success')) !!},
            color: 'green',
            position: 'topRight',
        });
    </script>
@endif

<div class="page-content">
    <div class="container">
        <div class="row cart">
            <div class="col-md-12">
                @php
                    // Récupère les sessions en s'assurant que ce sont des tableaux
                    $arr_cart_room_id = session()->get('cart_room_id') ?? [];
                    $arr_cart_checkin_date = session()->get('cart_checkin_date') ?? [];
                    $arr_cart_checkout_date = session()->get('cart_checkout_date') ?? [];
                    $arr_cart_adult = session()->get('cart_adult') ?? [];
                    $arr_cart_children = session()->get('cart_children') ?? [];

                    $total_price = 0;
                @endphp

                @if(!empty($arr_cart_room_id))
                    <div class="table-responsive">
                        <table class="table table-bordered table-cart">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Serial</th>
                                    <th>Photo</th>
                                    <th>Room Info</th>
                                    <th>Price/Night</th>
                                    <th>Checkin</th>
                                    <th>Checkout</th>
                                    <th>Guests</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($arr_cart_room_id as $i => $room_id)
                                    @php
                                        $room_data = DB::table('rooms')->find($room_id);
                                        if(!$room_data) continue;

                                        $checkin = $arr_cart_checkin_date[$i] ?? null;
                                        $checkout = $arr_cart_checkout_date[$i] ?? null;

                                        // Sécurise le calcul des nuits
                                        $nights = 0;
                                        if($checkin && $checkout){
                                            // Si format d/m/Y
                                            if(strpos($checkin,'/') !== false && strpos($checkout,'/') !== false){
                                                $d1 = explode('/', $checkin);
                                                $d2 = explode('/', $checkout);
                                                if(count($d1) === 3 && count($d2) === 3){
                                                    $t1 = strtotime($d1[2].'-'.$d1[1].'-'.$d1[0]);
                                                    $t2 = strtotime($d2[2].'-'.$d2[1].'-'.$d2[0]);
                                                    $nights = max(0, ($t2-$t1)/86400);
                                                }
                                            } else { // Si format Y-m-d
                                                $t1 = strtotime($checkin);
                                                $t2 = strtotime($checkout);
                                                $nights = max(0, ($t2-$t1)/86400);
                                            }
                                        }

                                        $subtotal = $room_data->price * $nights;
                                        $total_price += $subtotal;

                                        $adult = $arr_cart_adult[$i] ?? 1;
                                        $children = $arr_cart_children[$i] ?? 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="#" class="cart-delete-link" onclick="return confirm('Are you sure?');">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><img src="{{ asset('uploads/'.$room_data->featured_photo) }}" alt="Room Photo"></td>
                                        <td><a href="{{ route('room_detail', $room_data->id) }}" class="room-name">{{ $room_data->name }}</a></td>
                                        <td>${{ number_format($room_data->price,2) }}</td>
                                        <td>{{ $checkin ?? '-' }}</td>
                                        <td>{{ $checkout ?? '-' }}</td>
                                        <td>
                                            Adult: {{ $adult }}<br>
                                            Children: {{ $children }}
                                        </td>
                                        <td>${{ number_format($subtotal,2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="8" class="tar"><b>Total:</b></td>
                                    <td><b>${{ number_format($total_price,2) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="checkout mb_20">
                        <a href="#" class="btn btn-primary bg-website">Checkout</a>
                    </div>
                @else
                    <div class="text-danger mb-30">
                        Cart is empty!
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
