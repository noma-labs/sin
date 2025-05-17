@extends("photo.main")

@section("content")
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
        </div>
    </form>

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
                        alt="..."
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
