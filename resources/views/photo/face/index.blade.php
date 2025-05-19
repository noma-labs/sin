@extends("photo.main")

@section("title", "Volti")

@section("content")
    <div class="d-flex flex-wrap gap-2">
        @foreach ($faces as $face)
            <a href="{{ route("photos.index", ['name'=> $face->name]) }}" >

             <figure class="figure m-1" style="width: 15rem">
                    <figcaption class="figure-caption">
                      {{ $face->name}}
                    </figcaption>
                     <figcaption class="figure-caption">
                      Appare in {{ $face->count}}
                    </figcaption>
                    <img

                    />
                </figure>
        </a>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $faces->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>

@endsection
