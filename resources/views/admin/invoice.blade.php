


@extends('admin.layout.app')

@section('heading', 'Order Invoice')

@section('main_content')
<div class="section-body">
    <div class="invoice">
        <div class="invoice-print">
            <div class="row">
                <div class="col-lg-12">
                    <div class="invoice-title">
                        <h2>Invoice</h2>
                        <div class="invoice-number">Order #{{$order->order_no}}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            @if($order->customer)
                                <address>
                                    <strong>Invoice To</strong><br>
                                    {{ $order->customer->name }}<br>
                                    {{ $order->customer->address ?? '—' }}<br>
                                    {{ $order->customer->state ?? '—' }},
                                    {{ $order->customer->city ?? '—' }},
                                    {{ $order->customer->zip ?? '—' }}
                                </address>
                            @else
                                <em>Customer info not available</em>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-right">
                            <address>
                                <strong>Invoice Date</strong><br>
                                {{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y H:i') }}
                            </address>
                            @if($order->payment_method === 'Stripe')
                                <address>
                                    <strong>Payment Method</strong><br>
                                    {{ $order->payment_method }}<br>
                                    Card: {{ ucfirst($order->card_brand) }} ending in {{ $order->card_last_digit }}
                                </address>
                            @else
                                <address>
                                    <strong>Payment Method</strong><br>
                                    {{ $order->payment_method }}
                                </address>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="section-title">Order Summary</div>
                    <p class="section-lead">Hotel room informations</p>
                    <hr class="invoice-above-table">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-md">
                            <tr>
                                <th>SL</th>
                                <th>Room Name</th>
                                <th class="text-center">Checkin Date</th>
                                <th class="text-center">Checkout Date</th>
                                <th class="text-right">Number of Adult</th>
                                <th class="text-right">Number of Children</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                           @foreach($order_detail as $item)
                            @php
                               $room_data = \App\Models\Room::where('id', $item->room_id)->first();
                            @endphp
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$room_data->name}}</td>
                                <td class="text-center">{{$item->checkin_date}}</td>
                                <td class="text-center">{{$item->checkout_date}}</td>
                                <td class="text-right">{{$item->adult}}</td>
                                <td class="text-right">{{$item->children}}</td>
                                <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                           @endforeach
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-12 text-right">
                            <div class="invoice-detail-item">
                                <div class="invoice-detail-name">Total</div>
                                <div class="invoice-detail-value invoice-detail-value-lg">
                                    ${{ number_format($order->paid_amount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <hr class="about-print-button">
        <div class="text-md-right">
            <a href="javascript:window.print();" class="btn btn-warning btn-icon icon-left text-white print-invoice-button">
                <i class="fa fa-print"></i> Print
            </a>
        </div>
    </div>
</div>
@endsection
