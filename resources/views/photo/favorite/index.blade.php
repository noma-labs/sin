@extends("photo.main")

@section("content")
    <div class="d-print-none">
        <form
            action="{{ route("photos.favorite.index") }}"
            method="GET"
            class="mb-3"
        >
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                @foreach ($years as $year)
                    <button
                        type="submit"
                        name="year"
                        value="{{ $year->year }}"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        {{ $year->year }}
                        <span class="badge text-bg-secondary">
                            {{ $year->count }}
                        </span>
                    </button>
                @endforeach

                <a
                    href="{{ route("photos.favorite.download") }}"
                    class="btn btn-sm btn-outline-secondary"
                >
                    Download all
                </a>
            </div>
        </form>
    </div>

    <a
        href="{{ route("photos.slideshow", array_merge(request()->all(), ["favorite" => 1])) }}"
        class="d-print-none btn btn-primary m-3"
        target="_blank"
    >
        Slideshow
    </a>

    @can("photo.download")
        <a
            href="{{ route("photos.legend") }}"
            class="d-print-none btn btn-secondary m-3"
        >
            Scarica didascalie
        </a>
    @endcan

    <div class="d-flex flex-wrap">
        @foreach ($photos as $photo)
            <a href="{{ route("photos.show", $photo->id) }}">
                <figure class="figure m-1" style="width: 20rem">
                    <img
                        src="{{ route("photos.preview", $photo->id) }}"
                        class="figure-img img-fluid rounded"
                        alt="{{ $photo->description }}"
                    />
                    <figcaption class="figure-caption">
                        <div class="d-none d-print-block">
                            {{ $photo->file_name }}
                        </div>
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

    <div class="d-print-none d-flex justify-content-center">
        {{ $photos->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>

    @if ($enrico)
        <div class="row">
            <h2>Metadata from enrico DBF</h2>
            <ul>
                @foreach ($enrico as $photo)
                    <li>
                        <span class="badge text-bg-secondary">
                            {{ $photo->data }}
                        </span>
                        {{ $photo->datnum }}
                        {{ $photo->anum }}
                        {{ $photo->localita }}
                        {{ $photo->argomento }}
                        {{ $photo->descrizione }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
