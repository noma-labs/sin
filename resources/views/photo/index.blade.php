@extends("photo.main")

@section("content")
    <div class="d-flex flex-wrap gap-2">
        @foreach ($years as $year)
            <a
                href="{{ route("photos.index", ["year" => $year->year, "name" => request("name"), "view" => request("view", "grid")]) }}"
                class="btn btn-sm btn-outline-secondary"
            >
                {{ $year->year }}
                <span class="badge text-bg-secondary">
                    {{ $year->count }}
                </span>
            </a>
        @endforeach
    </div>

    @php($currentView = request("view", "grid"))
    <div class="btn-group mb-3" role="group" aria-label="Selettore vista">
        <a
            class="btn btn-outline-secondary {{ $currentView === "grid" ? "active" : "" }}"
            href="{{ route("photos.index", array_merge(request()->except("view"), ["view" => "grid"])) }}"
        >
            Griglia
        </a>
        <a
            class="btn btn-outline-secondary {{ $currentView === "list" ? "active" : "" }}"
            href="{{ route("photos.index", array_merge(request()->except("view"), ["view" => "list"])) }}"
        >
            Lista
        </a>
    </div>

    <a
        href="{{ route("photos.slideshow", request()->all()) }}"
        class="btn btn-primary m-3"
        target="_blank"
    >
        Slideshow
    </a>

    @if ($currentView === "grid")
        <div class="d-flex flex-wrap">
            @foreach ($photos as $photo)
                <a href="{{ route("photos.show", $photo->id) }}">
                    <figure class="figure m-1" style="width: 30rem">
                        <figcaption class="figure-caption">
                            <div>{{ $photo->file_name ?? "" }}</div>
                            <div>
                                {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                            </div>
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
    @elseif ($currentView === "list")
        <ul class="list-group">
            @foreach ($photos as $photo)
                <li class="list-group-item d-flex align-items-center">
                    <img
                        src="{{ route("photos.preview", $photo->id) }}"
                        alt="{{ $photo->description }}"
                        class="me-3 rounded"
                        style="width: 80px; height: auto"
                    />
                    <div class="flex-grow-1">
                        <div class="fw-semibold">
                            {{ $photo->file_name ?? "—" }}
                        </div>
                        <div class="text-muted small">
                            {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                        </div>
                        @if ($photo->subjects)
                            <div class="small">{{ $photo->subjects }}</div>
                        @endif
                    </div>
                    <a
                        href="{{ route("photos.show", $photo->id) }}"
                        class="btn btn-sm btn-outline-primary"
                    >
                        Apri
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

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
