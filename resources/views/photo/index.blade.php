@extends("photo.main")

@section("content")
    <div class="d-flex flex-wrap gap-2">
        @foreach ($years as $year)
            <a
                href="{{ route("photos.index", ["year" => $year->year, "name" => request("name")]) }}"
                class="btn btn-sm btn-outline-secondary"
            >
                {{ $year->year }}
                <span class="badge text-bg-secondary">
                    {{ $year->count }}
                </span>
            </a>
        @endforeach
    </div>

    <a
        href="{{ route("photos.slideshow", request()->all()) }}"
        class="btn btn-primary m-3"
        target="_blank"
    >
        Slideshow
    </a>

    <div class="d-flex flex-wrap">
        @foreach ($photos as $photo)
            <a href="{{ route("photos.show", $photo->id) }}">
                <figure class="figure m-1" style="width: 30rem">
                    <figcaption class="figure-caption">
                        {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                    </figcaption>
                    <img
                        src="{{ route("photos.preview", $photo->id) }}"
                        class="figure-img img-fluid rounded"
                        alt="{{ $photo->description }}"
                    />
                </figure>
            </a>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
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
