@extends('admin.layout.app')

@section('heading', 'Create Manual Booking')

@section('right_top_button')
  <a href="{{ route('admin_order_view') }}" class="btn btn-primary"><i class="fa fa-eye"></i> View All Orders</a>
@endsection


@section('main_content')

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.booking.store') }}" method="post">
                        @csrf
                        
                        {{-- Existing Client --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Existing Client (optional)</label>
                            <select name="customer_id" class="form-control">
                                <option value="">Select Client</option>
                                @foreach(\App\Models\Customer::all() as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Manual Client Info --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Or Enter Client Info</label>
                            <input type="text" name="name" placeholder="Client Name" class="form-control mb-2">
                            <input type="email" name="email" placeholder="Email" class="form-control mb-2">
                            <input type="text" name="phone" placeholder="Phone" class="form-control">
                        </div>

                        {{-- Room --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Room *</label>
                            <select name="room_id" class="form-control">
                                <option value="">Select Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">
                                        {{ $room->name }} (${{ $room->price }}/night)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dates --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Check-in *</label>
                            <input type="date" name="checkin" class="form-control">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Check-out *</label>
                            <input type="date" name="checkout" class="form-control">
                        </div>

                        {{-- Guests --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Adults *</label>
                            <input type="number" name="adult" class="form-control" min="1" value="1">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Children</label>
                            <input type="number" name="children" class="form-control" min="0" value="0">
                        </div>

                        {{-- Payment Status --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Payment Status *</label>
                            <select name="payment_status" class="form-control">
                                <option value="pending">Pending (Pay later)</option>
                                <option value="paid">Completed</option>
                            </select>
                        </div>

                        {{-- Paid Amount --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Paid Amount</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" value="0">
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary w-100">
                                Create Booking
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSelect = document.getElementById('room_id');
    const checkin = document.getElementById('checkin');
    const checkout = document.getElementById('checkout');
    const totalInput = document.getElementById('total_price');

    function calculateTotal() {
        const roomPrice = parseFloat(roomSelect.selectedOptions[0]?.dataset.price || 0);
        const checkinDate = new Date(checkin.value);
        const checkoutDate = new Date(checkout.value);

        if (!roomPrice || !checkin.value || !checkout.value) {
            totalInput.value = '';
            return;
        }

        const diffTime = checkoutDate - checkinDate;
        const nights = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
        totalInput.value = (roomPrice * nights).toFixed(2);
    }

    roomSelect.addEventListener('change', calculateTotal);
    checkin.addEventListener('change', calculateTotal);
    checkout.addEventListener('change', calculateTotal);
});
</script>
@endsection