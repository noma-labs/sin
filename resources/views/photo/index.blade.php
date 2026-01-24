@extends("photo.main")

@section("content")
    <div class="d-flex flex-wrap gap-2 my-3">
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
        <a
            class="btn btn-outline-secondary {{ $currentView === "rolls" ? "active" : "" }}"
            href="{{ route("photos.index", array_merge(request()->except("view"), ["view" => "rolls"])) }}"
        >
            Stricie
        </a>
    </div>

    <a
        href="{{ route("photos.slideshow", request()->all()) }}"
        class="btn btn-primary mb-3"
        target="_blank"
    >
        Slideshow
    </a>

    @if ($currentView === "grid")
        <div class="d-flex flex-wrap">
            @foreach ($photos as $photo)
                <a href="{{ route("photos.show", $photo->id) }}">
                    <figure class="figure m-1" style="width: 30rem">
                        <div class="position-relative">
                            <img
                                src="{{ route("photos.preview", $photo->id) }}"
                                class="figure-img img-fluid rounded"
                                alt="{{ $photo->description }}"
                            />
                            <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white">
                                <div class="small">{{ $photo->file_name ?? "" }}</div>
                                <div class="small">
                                    {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                </div>
                            </div>
                        </div>
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
    @elseif ($currentView === "rolls")
        @if ($rolls && $rolls->count())
            <div class="accordion" id="rollsAccordion">
                @foreach ($rolls as $index => $roll)
                    @php($rollKey = $roll->roll_key)
                    @php($collapseId = 'roll-collapse-'.$index)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-{{ $index }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                <div class="d-flex align-items-center w-100 gap-3">
                                    <span class="fw-semibold">Striscia: {{ $rollKey }}</span>
                                    <span class="badge text-bg-secondary">{{ ($groups[$rollKey] ?? collect())->count() }} foto</span>
                                    <span class="ms-auto text-muted small">
                                        {{ $roll->data ?? '' }}
                                        @if (!empty($roll->descrizione))
                                            — {{ $roll->descrizione }}
                                        @endif
                                        @if (!empty($roll->localita))
                                            — {{ $roll->localita }}
                                        @endif
                                        @if (!empty($roll->argomento))
                                            — {{ $roll->argomento }}
                                        @endif
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}" data-bs-parent="#rollsAccordion">
                            <div class="accordion-body">
                                <div class="d-flex flex-wrap">
                                    @foreach (($groups[$rollKey] ?? collect()) as $photo)
                                        <a href="{{ route("photos.show", $photo->id) }}" class="text-decoration-none">
                                            <figure class="figure m-1" style="width: 18rem">
                                                <div class="position-relative">
                                                    <img
                                                        src="{{ route("photos.preview", $photo->id) }}"
                                                        class="figure-img img-fluid rounded"
                                                        alt="{{ $photo->description }}"
                                                    />
                                                    <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white">
                                                        <div class="small">{{ $photo->file_name ?? "" }}</div>
                                                        <div class="small">
                                                            {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </figure>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">Nessuna striscia trovata.</div>
        @endif
    @endif

    <div class="d-flex justify-content-center">
        @if ($currentView === "rolls" && $rolls)
            {{ $rolls->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
        @else
            {{ $photos->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
        @endif
    </div>
@endsection
