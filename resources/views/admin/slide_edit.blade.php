@extends('admin.layout.app')

@section('heading', 'Slide Edit')

@section('right_top_button')
  <a href="{{route('admin_slide_view')}}" class="btn btn-primary"><i class="fa fa-plus"></i>View All</a>
@endsection

@section('main_content')

                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{route('admin_slide_update', $slide_data->id)}}" method="post"enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            
                                            <div class="col-md-12">
                                                <div class="mb-4">
                                                    <label class="form-label">Existing Photo </label>
                                                    <div>
                                                       <img class="w_200" src="{{ asset('uploads/' . $slide_data->photo) }}" alt="">

                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Change Photo </label>
                                                    <div>
                                                        <input type="file" name="photo">
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Heading</label>
                                                    <input type="text" class="form-control" name="heading" value="{{ old('heading', $slide_data->heading) }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Text</label>
                                                    
                                                    <textarea name="text" class="form-control h_100" cols="30" rows="10">
                                                        {{$slide_data->text}}
                                                    </textarea>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Button Text</label>
                                                    <input type="text" class="form-control" name="button_text" value="{{ old('button_text', $slide_data->button_text) }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Button URL</label>
                                                    <input type="text" class="form-control" name="button_url" value="{{ old('button_url', $slide_data->button_url) }}">
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