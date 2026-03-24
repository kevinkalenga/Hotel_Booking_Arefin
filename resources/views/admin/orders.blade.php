@extends('admin.layout.app')

@section('heading', 'Customer Orders')

@section('main_content')


 <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Order No</th>
                                                    <th>Payment Method</th>
                                                    <th>Booking Date</th>
                                                    <th>Paid Amount</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                              @foreach($orders as $row)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$row->order_no}}</td>
                                                    <td>{{$row->payment_method}}</td>
                                                    <td>{{$row->booking_date}}</td>
                                                    <td>{{$row->paid_amount}}</td>
                                                    <td>
                                                        <span class="badge {{ $row->status == 'Completed' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ $row->status }}
                                                        </span>
                                                    </td>
                                                    <td class="pt_10 pb_10">
                                                        
                                                        <a href="{{route('admin_invoice', $row->id)}}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                        <a href="{{route('admin_order_delete', $row->id)}}" class="btn btn-danger" onClick="return confirm('Are you sure?');"><i class="fa fa-trash"></i></a>
                                                        @if($row->status == 'Pending')
                                                            <a href="{{ route('admin_order_complete', $row->id) }}" 
                                                               class="btn btn-success"
                                                               onclick="return confirm('Customer arrived? Confirm check-in')">
                                                                Check-in
                                                            </a>
                                                        @endif
        
                                                    </td>
                                                   
                                                </tr>
                                               @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


@endsection