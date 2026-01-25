@extends("photo.main")

@section("content")
    <div class="d-flex flex-wrap gap-2 my-3">
        <div class="btn-group me-3" role="group" aria-label="Filtro sorgente">
            @php($currentSource = strtolower(request("source", "")))
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["source" => null])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentSource === "" ? "active" : "" }}"
            >
                Tutte
            </a>
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["source" => "foto"])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentSource === "foto" ? "active" : "" }}"
            >
                Foto
            </a>
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["source" => "dia120"])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentSource === "dia120" || $currentSource === "dia120" ? "active" : "" }}"
            >
                Dia 120
            </a>
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["source" => "slide"])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentSource === "slide" || $currentSource === "slide" ? "active" : "" }}"
            >
                Slide
            </a>
        </div>
        <div class="btn-group me-3" role="group" aria-label="Ordina per">
            @php($currentOrder = strtolower(request("order", "datnum")))
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["order" => "datnum"])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentOrder === "datnum" ? "active" : "" }}"
            >
                Ordina per Datnum
            </a>
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["order" => "data"])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentOrder === "data" ? "active" : "" }}"
            >
                Ordina per Data
            </a>
        </div>
        @foreach ($years as $year)
            <a
                href="{{ route("photos.stripes.index", ["year" => $year->year, "name" => request("name"), "source" => request("source"), "order" => request("order", "datnum")]) }}"
                class="btn btn-sm btn-outline-secondary"
            >
                {{ $year->year }}
                <span class="badge text-bg-secondary">
                    {{ $year->count }}
                </span>
            </a>
        @endforeach
    </div>
    @if ($stripes && $stripes->count())
        <div class="accordion" id="stripesAccordion">
            @foreach ($stripes as $index => $stripe)
                @php($collapseId = "stripe-collapse-" . $index)
                @php($photoCount = isset($stripe->photos) ? count($stripe->photos) : 0)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ $index }}">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="false"
                            aria-controls="{{ $collapseId }}"
                        >
                            <div class="d-flex align-items-center w-100 gap-3">
                                @php(
                                    ($sourceBadge = match (strtolower($stripe->source)) {
                                        "foto" => "text-bg-primary",
                                        "slide" => "text-bg-info",
                                        "dia120" => "text-bg-warning",
                                        default => "text-bg-secondary",
                                    })
                                )
                                <span class="badge {{ $sourceBadge }}">
                                    {{ strtoupper($stripe->source) }}
                                </span>
                                <span class="fw-semibold">
                                    {{ $stripe->datnum }}
                                    @if (! empty($stripe->anum) && $stripe->anum !== $stripe->datnum)
                                            — {{ $stripe->anum }}
                                    @endif

                                    ({{ $stripe->data }})

                                    @if ($photoCount == 0)
                                        <span class="badge text-bg-danger">
                                            senza foto
                                        </span>
                                    @elseif (! is_null($stripe->nfo) && $stripe->nfo > $photoCount)
                                        <span class="badge text-bg-warning">
                                            {{ $stripe->nfo - $photoCount }}
                                            foto mancanti
                                        </span>
                                    @else
                                        <span class="badge text-bg-success">
                                            {{ $photoCount }} foto
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div
                        id="{{ $collapseId }}"
                        class="accordion-collapse collapse"
                        aria-labelledby="heading-{{ $index }}"
                        data-bs-parent="#stripesAccordion"
                    >
                        <div class="accordion-body">
                            <div
                                class="mb-3 d-flex flex-wrap gap-3 align-items-center"
                            >
                                <span class="fw-semibold">
                                    {{ $stripe->datnum }}
                                    @if (! empty($stripe->anum) && $stripe->anum !== $stripe->datnum)
                                            — {{ $stripe->anum }}
                                    @endif
                                </span>
                                @if (! empty($stripe->data))
                                    <span>
                                        <span class="text-muted">Data:</span>
                                        <span class="fw-semibold">
                                            {{ $stripe->data }}
                                        </span>
                                    </span>
                                @endif

                                @if (! is_null($stripe->nfo))
                                    <span>
                                        <span class="text-muted">NFO:</span>
                                        <span class="fw-semibold">
                                            {{ $stripe->nfo }}
                                        </span>
                                    </span>
                                @endif

                                @if (! empty($stripe->localita))
                                    <span>
                                        <span class="text-muted">
                                            Località:
                                        </span>
                                        <span class="fw-semibold">
                                            {{ $stripe->localita }}
                                        </span>
                                    </span>
                                @endif

                                @if (! empty($stripe->argomento))
                                    <span>
                                        <span class="text-muted">
                                            Argomento:
                                        </span>
                                        <span class="fw-semibold">
                                            {{ $stripe->argomento }}
                                        </span>
                                    </span>
                                @endif

                                @if (! empty($stripe->descrizione))
                                    <span>
                                        <span class="text-muted">
                                            Descrizione:
                                        </span>
                                        <span class="fw-semibold">
                                            {{ $stripe->descrizione }}
                                        </span>
                                    </span>
                                @endif
                            </div>
                            @if ($photoCount)
                                <div class="d-flex flex-wrap">
                                    @foreach ($stripe->photos as $photo)
                                        <a
                                            href="{{ route("photos.show", $photo->id) }}"
                                            class="text-decoration-none"
                                        >
                                            <figure
                                                class="figure m-1"
                                                style="width: 18rem"
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
                                                        <div class="small">
                                                            {{ $photo->file_name ?? "" }}
                                                        </div>
                                                        <div class="small">
                                                            {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </figure>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info m-0">
                                    Nessuna foto collegata a questa striscia.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Nessuna striscia trovata.</div>
    @endif

    <div class="d-flex justify-content-center">
        {{ $stripes->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>
@endsection
