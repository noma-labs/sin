@extends("photo.main")

@section("title", "Volti")

@section("content")
    <div class="d-flex flex-wrap">
        @foreach ($faces as $face)
            <a href="{{ route("photos.show", $photo->id) }}">
                <figure class="figure m-1" style="width: 30rem">
                    <img
                        src="{{ route("photos.preview", $photo->id) }}"
                        class="figure-img img-fluid rounded"
                        alt="{{ $photo->description }}"
                    />
                    <figcaption class="figure-caption">
                        <p class="fw-bold fs-4 lh-1">
                            {{ $photo->location }}
                        </p>
                        <p class="fw-bold fs-7 lh-1">
                            @if ($photo->taken_at)
                                @if ($photo->taken_at->format("m-d") === "01-01")
                                    {{ $photo->taken_at->format("Y") }}
                                @else
                                    {{ $photo->taken_at->format("d/m/Y") }}
                                @endif
                            @endif
                        </p>

                        @if ($photo->description)
                            <p class="lh-1">{{ $photo->description }}</p>
                        @endif
                    </figcaption>
                </figure>
            </a>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $photos->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>

@endsection
