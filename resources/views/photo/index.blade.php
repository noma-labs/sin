@extends("layouts.app")

@section("title", "Foto (Enrico)")

@section("content")
    <form action="{{ route("photos.index") }}" method="GET" class="mb-3">
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
            <div class="card m-1" style="width: 18rem; ">
                <img
                    src="{{ asset("storage/foto-sport/$photo->folder_title/$photo->file_name") }}"
                    alt="Photo"
                    class="card-img-top"
                    style="height: auto"
                />
                <div class="card-body">
                    <p class="mb-1">
                        <strong>File Name:</strong>
                        {{ $photo->file_name }}
                    </p>

                    <p class="mb-1">
                        <strong>Data:</strong>
                        {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                    </p>
                    <p class="mb-1">
                        <strong>Location:</strong>
                        {{ $photo->location }}
                    </p>
                    <p class="mb-1">
                        <strong>Description:</strong>
                        {{ $photo->description }}
                    </p>
                    <a
                        href="{{ route("photos.show", $photo->sha) }}"
                        class="btn btn-primary mt-2 btn-sm"
                    >
                        View
                    </a>

                    @if ($photo->favorite === 0)
                        <form
                            action="{{ route("photos.favorite", $photo->sha) }}"
                            class="mt-2"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-success btn-sm"
                            >
                                Favorite
                            </button>
                        </form>
                    @else
                        <form
                            action="{{ route("photos.unfavorite", $photo->sha) }}"
                            method="POST"
                            class="mt-2"
                        >
                            @csrf
                            @method("PUT")
                            <button
                                type="submit"
                                class="btn btn-danger btn-sm"
                            >
                                Unfavorite
                            </button>
                        </form>
                    @endif
                    <a
                        href="{{ route("photos.download", $photo->sha) }}"
                        class="btn btn-secondary btn-sm mt-2"
                    >
                        Download
                    </a>
                </div>
            </div>
        @endforeach
    </div>

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

    @endsection
