@extends('admin.layout.app')

@section('heading', 'View Rooms')

@section('right_top_button')
  <a href="{{route('admin_room_add')}}" class="btn btn-primary"><i class="fa fa-plus"></i>Add New</a>
@endsection

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
                                                    <th>Photo</th>
                                                    <th>Name</th>
                                                    <th>Price (per night)</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                              @php $i=0; @endphp 
                                              @foreach($rooms as $row)
                                              @php $i++; @endphp
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>
                                                        <img src="{{asset('uploads/'.$row->featured_photo)}}" alt="" class="w_200">
                                                    </td>
                                                    <td>
                                                       {{$row->name}}
                                                    </td>
                                                    <td>
                                                        ${{$row->price}}
                                                    </td>
                                                  
                                                    
                                                    <td class="pt_10 pb_10">
                                                        <button class="btn btn-warning" data-toggle="modal" data-target="#exampleModal{{$i}}">
                                                            Detail
                                                        </button>
                                                         <a href="{{route('admin_room_gallery', $row->id)}}" class="btn btn-success">Photo Gallery</a>
                                                        <a href="{{route('admin_room_edit', $row->id)}}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                        <a href="{{route('admin_room_delete', $row->id)}}" class="btn btn-danger" onClick="return confirm('Are you sure?');"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                   
                                                </tr>
                                               
                                                <div class="modal fade" id="exampleModal{{$i}}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Room Detail</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Phtoto</label></div>
                                                                     <div class="col-md-8">
                                                                        <img src="{{asset('uploads/'.$row->featured_photo)}}" class="w_200" alt="">
                                                                    </div>
                                                                   
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Name</label></div>
                                                                     <div class="col-md-8">{{$row->name}}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Description</label></div>
                                                                    <div class="col-md-8">{!! $row->description !!}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Price (per night)</label></div>
                                                                    <div class="col-md-8">${{$row->price}}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Total Rooms</label></div>
                                                                    <div class="col-md-8">{{$row->total_rooms}}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Total Amenities</label></div>
                                                                    <div class="col-md-8">
                                                                        @php 
                                                                           $arr = explode(',', $row->amenities);
                                                                            foreach ($arr as $amenity) {
                                                                                echo trim($amenity) . '<br>';
                                                                            }

                                                                        @endphp
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Size</label></div>
                                                                    <div class="col-md-8">{{$row->size}}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Total Beds</label></div>
                                                                    <div class="col-md-8">{{$row->total_beds}}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Total Bathrooms</label></div>
                                                                    <div class="col-md-8">{{$row->total_bathrooms}}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Total Balconies</label></div>
                                                                    <div class="col-md-8">{{$row->total_balconies}}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Total Guests</label></div>
                                                                    <div class="col-md-8">{{$row->total_guests}}</div>
                                                                </div>
                                                                <div class="form-group row bdb1 pt_10 mb_0">
                                                                    <div class="col-md-4"><label class="form-label">Video</label></div>
                                                                    <div class="iframe-container1">
                                                                       <iframe width="560" height="315" src="https://www.youtube.com/embed/{{$row->video_id}}?si=mwH0OVjL_dfKXhZs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe> 
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                               
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