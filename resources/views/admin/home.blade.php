@extends('admin.layout.app')

@section('heading', 'Dashboard') 



@section('main_content')
   
         <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Completed Orders</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_completed_orders}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Pending Orders</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_pending_orders}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fa fa-bullhorn"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Active Customers</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_active_customers}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Pending Customers</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_pending_customers}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Rooms</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_rooms}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-dark">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Subscribers</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_subscribers}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <section class="section">
                            <div class="section-header">
                                <h1>Recent Order</h1>
                               
                            </div>

                        </section>
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
                                                    <td class="pt_10 pb_10">
                                                        
                                                        <a href="{{route('admin_invoice', $row->id)}}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                        <a href="{{route('admin_order_delete', $row->id)}}" class="btn btn-danger" onClick="return confirm('Are you sure?');"><i class="fa fa-trash"></i></a>
        
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
                    </div>

                </div>

@endsection

