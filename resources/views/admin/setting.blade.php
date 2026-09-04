@extends('admin.layout.app')

@section('heading', 'Setting')


@section('main_content')

                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{route('admin_setting_update', $setting_data->id)}}" method="post"enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            
                                            <div class="col-md-12">
                                              <div class="row">
                                                  <div class="col-md-6">
                                                      
                                                    <div class="mb-4">
                                                        <label class="form-label">Existing Logo </label>
                                                        <div>
                                                        <img class="w_200" src="{{ asset('uploads/' . $setting_data->logo) }}" alt="">

                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="form-label">Change Logo </label>
                                                        <div>
                                                            <input type="file" name="logo">
                                                        </div>
                                                    </div>
                                                  
                                                
                                                  </div>
                                                  <div class="col-md-6">
                                                    <div class="mb-4">
                                                        <label class="form-label">Existing Favicon </label>
                                                        <div>
                                                        <img class="w_50" src="{{ asset('uploads/' . $setting_data->favicon) }}" alt="">

                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="form-label">Change Favicon </label>
                                                        <div>
                                                            <input type="file" name="favicon">
                                                        </div>
                                                    </div>

                                                  </div>
                                              </div>
                                               
                                             
                                                <div class="mb-4">
                                                    <label class="form-label">Top Bar Phone</label>
                                                    <input type="text" class="form-control" name="top_bar_phone" value="{{$setting_data->top_bar_phone}}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Top Bar Email</label>
                                                    
                                                   <input type="text" class="form-control" name="top_bar_email" value="{{$setting_data->top_bar_email}}">
                                                </div> 
                                              
                                                
                                                
                                                <div class="mb-4">
                                                    <label class="form-label"></label>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



@endsection