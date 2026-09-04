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
                                                {{-- <div class="mb-4">
                                                    <label class="form-label">Heading</label>
                                                    <input type="text" class="form-control" name="heading" value="{{ old('heading', $slide_data->heading) }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Text</label>
                                                    
                                                    <textarea name="text" class="form-control h_100" cols="30" rows="10">
                                                        {{$slide_data->text}}
                                                    </textarea>
                                                </div> --}}
                                              
                                                
                                                
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