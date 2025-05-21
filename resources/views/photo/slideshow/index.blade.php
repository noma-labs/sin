@extends("layouts.blank")

@section("content")
    <div
        id="photoCarousel"
        class="carousel slide"
        data-bs-ride="carousel"
        data-bs-interval="{{ $every }}"
    >
        <div class="carousel-inner">
            @foreach ($photos as $index => $photo)
                <div class="carousel-item @if($index == 0) active @endif">
                    <img
                        src="{{ route("photos.preview", $photo->id) }}"
                        class="d-block w-100"
                        style="max-height: 100vh; object-fit: contain"
                        alt="{{ $photo->description }}"
                    />
                    <div class="carousel-caption d-none d-md-block">
                        <h5>{{ $photo->location }}</h5>
                        <p>{{ $photo->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#photoCarousel"
            data-bs-slide="prev"
        >
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#photoCarousel"
            data-bs-slide="next"
        >
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
@endsection
