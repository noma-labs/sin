@extends("layouts.blank")

@section("content")
    <div class="bg-dark min-vh-100">
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
                            class="d-flex flex-column mt-2"
                            style="min-height: 100vh"
                        >
                            <img
                                src="{{ route("photos.preview", $photo->id) }}"
                                class="d-block w-100 mb-3"
                                style="max-height: 87vh; object-fit: contain"
                                alt="{{ $photo->description }}"
                            />
                            <div
                                class="d-inline-block bg-body-secondary rounded p-2 mx-3"
                            >
                                @if ($photo->taken_at)
                                    <h5 class="text-black fw-bold m-0">
                                        @if ($photo->taken_at->format("m-d") === "01-01")
                                            {{ $photo->taken_at->format("Y") }}
                                        @else
                                            {{ $photo->taken_at->format("d/m/Y") }}
                                        @endif
                                    </h5>
                                    <p class="text-black fst-italic fs-5 m-0">
                                        {{ $photo->location }}
                                    </p>
                                @endif

                                @if ($photo->description)
                                    <p class="text-black fs-5 m-0">
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
    </div>
@endsection
