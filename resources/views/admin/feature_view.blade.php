@extends('admin.layout.app')

@section('heading', 'View Feature')

@section('right_top_button')
  <a href="{{route('admin_feature_add')}}" class="btn btn-primary"><i class="fa fa-plus"></i>Add New</a>
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
                                                    <th>Icon</th>
                                                    <th>Heading</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                              @foreach($features as $row)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>
                                                        <i class="{{$row->icon}} fz_40"></i>
                                                    </td>
                                                    <td>
                                                        {{$row->heading}}
                                                    </td>
                                                    <td>
                                                        <img src="{{asset('uploads/'.$row->photo)}}" alt="" class="w_200">
                                                    </td>
                                                    
                                                    <td class="pt_10 pb_10">
                                                        
                                                        <a href="{{route('admin_feature_edit', $row->id)}}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                        <a href="{{route('admin_feature_delete', $row->id)}}" class="btn btn-danger" onClick="return confirm('Are you sure?');"><i class="fa fa-trash"></i></a>
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