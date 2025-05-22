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
                <div
                    class="carousel-item @if($index == 0) active @endif text-center"
                >
                    <div
                        class="d-flex flex-column align-items-center justify-content-center"
                        style="min-height: 100vh"
                    >
                        <img
                            src="{{ route("photos.preview", $photo->id) }}"
                            class="d-block w-100 mb-3"
                            style="max-height: 80vh; object-fit: contain"
                            alt="{{ $photo->description }}"
                        />
                        <div
                            class="mx-3 d-inline-block bg-body-secondary rounded"
                        >
                            @if ($photo->taken_at)
                                <h5 class="text-black fw-bold mb-1">
                                    @if ($photo->taken_at->format("m-d") === "01-01")
                                        {{ $photo->taken_at->format("Y") }}
                                    @else
                                        {{ $photo->taken_at->format("d/m/Y") }}
                                    @endif
                                </h5>
                                <div class="text-black mb-1">
                                    ({{ $photo->location }})
                                </div>
                            @endif

                            @if ($photo->description)
                                <p class="text-black fs-6 mb-0">
                                    {{ $photo->description }}
                                </p>
                            @endif
                        </div>
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
