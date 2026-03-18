@extends('customer.layout.app')

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
                                    <h4>Completed Orders</h4>
                                </div>
                                <div class="card-body">
                                    8
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
                                    <h4>Pending Orders</h4>
                                </div>
                                <div class="card-body">
                                   5
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>
@endsection