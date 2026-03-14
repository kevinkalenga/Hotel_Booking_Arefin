@extends('front.layout.app')

@section('main_content')

<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>FAQ</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="accordion" id="accordionExample">

                    @foreach($faq_all as $i => $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $i }}">
                                <button 
                                    class="accordion-button @if($i != 0) collapsed @endif" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $i }}" 
                                    aria-expanded="{{ $i == 0 ? 'true' : 'false' }}" 
                                    aria-controls="collapse{{ $i }}">
                                    {{ $item->question }}
                                </button>
                            </h2>

                            <div 
                                id="collapse{{ $i }}" 
                                class="accordion-collapse collapse @if($i == 0) show @endif" 
                                aria-labelledby="heading{{ $i }}" 
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    {!! $item->answer !!}
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>
        </div>
    </div>
</div>

@endsection
