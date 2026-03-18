@extends('admin.layout.app')

@section('heading', 'Room Edit')

@section('right_top_button')
  <a href="{{route('admin_room_view')}}" class="btn btn-primary"><i class="fa fa-plus"></i>View All</a>
@endsection

@section('main_content')

                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{route('admin_room_update', $room_data->id)}}" method="post"enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            
                                            <div class="col-md-12">
                                                <div class="mb-4">
                                                    <label class="form-label">Existing featured Photo </label>
                                                    <div>
                                                       <img class="w_200" src="{{ asset('uploads/' . $room_data->featured_photo) }}" alt="">

                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Change featured Photo *</label>
                                                    <div>
                                                        <input type="file" name="featured_photo">
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Name *</label>
                                                    <input type="text" class="form-control" name="name" value="{{ $room_data->name }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Description *</label>
                                                    <textarea name="description" class="form-control snote" cols="30" rows="10">
                                                          {{ $room_data->description }}
                                                    </textarea>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Price *</label>
                                                    <input type="text" class="form-control" name="price" value="{{ $room_data->price }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Total Rooms *</label>
                                                    <input type="text" class="form-control" name="total_rooms" value="{{ $room_data->total_rooms }}">
                                                </div>
                                                    <div class="mb-4">
                                                    <label class="form-label">Amenities</label>
                                                    @foreach($all_amenities as $i => $item)
                                                       <div class="form-check">
                                                           <input 
                                                               class="form-check-input" 
                                                               type="checkbox" 
                                                               name="arr_amenities[]" 
                                                               id="defaultCheck{{ $i }}" 
                                                               value="{{ $item->id }}"
                                                               {{ in_array($item->id, $existing_amenities) ? 'checked' : '' }}
                                                           >
                                                           <label class="form-check-label" for="defaultCheck{{ $i }}">
                                                               {{ $item->name }}
                                                           </label>
                                                       </div>
                                                   @endforeach

                                                    
                                                     
                                                   
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Rooms Size</label>
                                                    <input type="text" class="form-control" name="size" value="{{ $room_data->size }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Beds</label>
                                                    <input type="text" class="form-control" name="total_beds" value="{{ $room_data->total_beds }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Bathrooms</label>
                                                    <input type="text" class="form-control" name="total_bathrooms" value="{{ $room_data->total_bathrooms }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Balconies</label>
                                                    <input type="text" class="form-control" name="total_balconies" value="{{ $room_data->total_balconies }}">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Guests</label>
                                                    <input type="text" class="form-control" name="total_guests" value="{{ $room_data->total_guests }}">
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label class="form-label">Video Preview</label>
                                                    <div class="iframe-container1">
                                                       <iframe width="560" height="315" src="https://www.youtube.com/embed/{{$room_data->video_id}}?si=mwH0OVjL_dfKXhZs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe> 
                                                    </div>
                                                </div>
                                                
                                                
                                                 <div class="mb-4">
                                                    <label class="form-label">Video Id</label>
                                                    <input type="text" class="form-control" name="video_id" value="{{ $room_data->video_id }}">
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