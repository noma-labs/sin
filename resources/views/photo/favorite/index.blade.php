@extends("photo.main")

@section("content")
    <form action="{{ route("photos.favorite.index") }}" method="GET" class="mb-3">
        {{ $photos_count }} photos
        <div class="d-flex flex-wrap gap-2">
            @foreach ($years as $year)
                <button
                    type="submit"
                    name="year"
                    value="{{ $year->year }}"
                    class="btn btn-sm btn-secondary"
                >
                    {{ $year->year }}
                    <span class="badge bg-light text-dark">
                        {{ $year->count }}
                    </span>
                </button>
            @endforeach
        </div>
    </form>

    <div class="d-flex flex-wrap">
        @foreach ($photos as $photo)
            <a href="{{ route("photos.show", $photo->sha) }}">
                <div class="card m-1" style="width: 30rem">
                    <img
                        src="{{ route("photos.preview", $photo->sha) }}"
                        alt="Photo"
                        class="card-img-top"
                        style="height: auto"
                    />

                    <div class="card-img-overlay">
                        <h5 class="card-text text-white">
                            {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                        </h5>

                        {{--
                            @if ($photo->favorite === 0)
                            <form action="{{ route("photos.favorite", $photo->sha) }}" >
                            @csrf
                            <button
                            type="submit"
                            class="btn btn-success btn-sm"
                            >
                            Favorite
                            </button>
                            </form>
                            @else
                            <form action="{{ route("photos.unfavorite", $photo->sha) }}" method="POST" >
                            @csrf
                            @method("PUT")
                            <button type="submit" class="btn btn-danger btn-sm">
                            Unfavorite
                            </button>
                            </form>
                            @endif
                        --}}
                        {{--
                            <a
                            href="{{ route("photos.download", $photo->sha) }}"
                            class="btn btn-secondary btn-sm mt-2"
                            >
                            Download
                            </a>
                        --}}
                    </div>
                </div>
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
