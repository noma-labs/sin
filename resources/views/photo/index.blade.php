@extends("photo.main")

@section("content")
    <div class="d-flex flex-wrap gap-2 my-3">
        @foreach ($years as $year)
            <a
                href="{{ route("photos.index", ["year" => $year->year, "name" => request("name"), "view" => request("view", "grid"), "no_strip" => request("no_strip")]) }}"
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
    @php($noStrip = request()->boolean("no_strip", false))
    @php($currentGroup = request("group"))
    <div class="d-flex gap-2 mb-3">
        <div class="btn-group" role="group" aria-label="Selettore vista">
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

        <div class="btn-group" role="group" aria-label="Filtro striscia">
            <a
                class="btn btn-outline-secondary {{ $noStrip ? "" : "active" }}"
                href="{{ route("photos.index", request()->except("no_strip")) }}"
            >
                Tutte
            </a>
            <a
                class="btn btn-outline-secondary {{ $noStrip ? "active" : "" }}"
                href="{{ route("photos.index", array_merge(request()->except("no_strip"), ["no_strip" => 1])) }}"
            >
                Senza Striscia
            </a>
        </div>

        <div class="btn-group" role="group" aria-label="Raggruppa per">
            <a
                class="btn btn-outline-secondary {{ empty($currentGroup) ? "active" : "" }}"
                href="{{ route("photos.index", array_merge(request()->except("group"), ["group" => null])) }}"
            >
                Nessun Gruppo
            </a>
            <a
                class="btn btn-outline-secondary {{ $currentGroup === "directory" ? "active" : "" }}"
                href="{{ route("photos.index", array_merge(request()->except("group"), ["group" => "directory"])) }}"
            >
                Per Cartella
            </a>
            <a
                class="btn btn-outline-secondary {{ $currentGroup === "stripe" ? "active" : "" }}"
                href="{{ route("photos.index", array_merge(request()->except("group"), ["group" => "stripe"])) }}"
            >
                Per Striscia
            </a>
        </div>
    </div>

    <a
        href="{{ route("photos.slideshow", request()->all()) }}"
        class="btn btn-primary mb-3"
        target="_blank"
    >
        Slideshow
    </a>

    @php($isGrouped = ! empty($currentGroup))
    @if ($isGrouped)
        <div class="accordion" id="photosGroupedAccordion">
            @foreach ($groups as $key => $group)
                @php($collapseId = "group-" . $key)
                <div class="accordion-item">
                    <h2
                        class="accordion-header"
                        id="heading-{{ $collapseId }}"
                    >
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="false"
                            aria-controls="{{ $collapseId }}"
                        >
                            {{ $group["label"] }}
                            <span class="badge text-bg-secondary ms-2">
                                {{ count($group["photos"]) }}
                            </span>
                        </button>
                    </h2>
                    <div
                        id="{{ $collapseId }}"
                        class="accordion-collapse collapse"
                        aria-labelledby="heading-{{ $collapseId }}"
                        data-bs-parent="#photosGroupedAccordion"
                    >
                        <div class="accordion-body">
                            @if ($currentView === "grid")
                                <div class="d-flex flex-wrap">
                                    @foreach ($group["photos"] as $photo)
                                        <a
                                            href="{{ route("photos.show", $photo->id) }}"
                                        >
                                            <figure
                                                class="figure m-1"
                                                style="width: 25rem"
                                            >
                                                <div class="position-relative">
                                                    <img
                                                        src="{{ route("photos.preview", $photo->id) }}"
                                                        class="figure-img img-fluid rounded"
                                                        alt="{{ $photo->description }}"
                                                    />
                                                    <div
                                                        class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white"
                                                    >
                                                        <span class="small">
                                                            {{ $photo->file_name ?? "" }}
                                                        </span>
                                                        <span class="small">
                                                            {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                                        </span>

                                                        @if ($photo->strip)
                                                            <span
                                                                class="badge text-bg-success"
                                                            >
                                                                {{ $photo->strip->datnum }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="badge text-bg-danger"
                                                            >
                                                                Senza Striscia
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </figure>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <ul class="list-group">
                                    @foreach ($group["photos"] as $photo)
                                        <li
                                            class="list-group-item d-flex align-items-center"
                                        >
                                            <img
                                                src="{{ route("photos.preview", $photo->id) }}"
                                                alt="{{ $photo->description }}"
                                                class="me-3 rounded"
                                                style="
                                                    width: 80px;
                                                    height: auto;
                                                "
                                            />
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">
                                                    {{ $photo->file_name ?? "—" }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                                </div>
                                                @if ($photo->strip)
                                                    <div class="mt-1">
                                                        <span
                                                            class="badge text-bg-success"
                                                        >
                                                            {{ $photo->strip->datnum }}
                                                        </span>
                                                    </div>
                                                @endif

                                                @if ($photo->subjects)
                                                    <div class="small">
                                                        {{ $photo->subjects }}
                                                    </div>
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
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @if ($currentView === "grid")
            <div class="d-flex flex-wrap">
                @foreach ($photos as $photo)
                    <a href="{{ route("photos.show", $photo->id) }}">
                        <figure class="figure m-1" style="width: 25rem">
                            <div class="position-relative">
                                <img
                                    src="{{ route("photos.preview", $photo->id) }}"
                                    class="figure-img img-fluid rounded"
                                    alt="{{ $photo->description }}"
                                />
                                <div
                                    class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white"
                                >
                                    <span class="small">
                                        {{ $photo->file_name ?? "" }}
                                    </span>
                                    <span class="small">
                                        {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                    </span>

                                    @if ($photo->strip)
                                        <span class="badge text-bg-success">
                                            {{ $photo->strip->datnum }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-danger">
                                            Senza Striscia
                                        </span>
                                    @endif
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
                            @if ($photo->strip)
                                <div class="mt-1">
                                    <span class="badge text-bg-success">
                                        {{ $photo->strip->datnum }}
                                    </span>
                                </div>
                            @endif

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
    @endif

    <div class="d-flex justify-content-center">
        {{ $photos->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>
@endsection
