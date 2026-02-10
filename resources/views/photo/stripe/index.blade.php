@extends("photo.main")

@section("content")
    <div class="mb-3">
        <form
            method="GET"
            action="{{ route("photos.stripes.index") }}"
            class="d-flex"
        >
            @foreach (request()->except(["page", "q"]) as $key => $value)
                @if (is_array($value))
                    @foreach ($value as $v)
                        <input
                            type="hidden"
                            name="{{ $key }}[]"
                            value="{{ $v }}"
                        />
                    @endforeach
                @else
                    @if (! is_null($value) && $value !== "")
                        <input
                            type="hidden"
                            name="{{ $key }}"
                            value="{{ $value }}"
                        />
                    @endif
                @endif
            @endforeach

            <div class="input-group input-group-sm">
                <input
                    type="text"
                    name="q"
                    class="form-control"
                    placeholder="Cerca per datnum, anum, località, argomento, descrizione"
                    value="{{ request("q") }}"
                />
                @if (request("q"))
                    <a
                        href="{{ route("photos.stripes.index", request()->except(["q", "page"])) }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>
                @endif

                <button class="btn btn-primary" type="submit">Cerca</button>
            </div>
        </form>
    </div>
    <div class="d-flex flex-wrap gap-2 my-3">
        <div class="btn-group me-3" role="group" aria-label="Filtro sorgente">
            @php($currentSource = strtolower(request("source", "")))
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["source" => null])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentSource === "" ? "active" : "" }}"
            >
                Tutti i formati
            </a>
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["source" => "foto"])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentSource === "foto" ? "active" : "" }}"
            >
                Foto analogiche
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
        <div class="btn-group me-3" role="group" aria-label="Filtro foto">
            @php($noPhotos = request("no_photos"))
            @php($mismatch = request("mismatch"))
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except(["page"]), ["no_photos" => null, "mismatch" => null])) }}"
                class="btn btn-sm btn-outline-secondary {{ empty($noPhotos) && empty($mismatch) ? "active" : "" }}"
            >
                Tutti
            </a>
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except(["page"]), ["no_photos" => 1, "mismatch" => null])) }}"
                class="btn btn-sm btn-outline-secondary {{ ! empty($noPhotos) ? "active" : "" }}"
            >
                Eventi senza foto
            </a>
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except(["page"]), ["mismatch" => 1, "no_photos" => null])) }}"
                class="btn btn-sm btn-outline-secondary {{ ! empty($mismatch) ? "active" : "" }}"
            >
                Eventi con foto mancanti o in eccesso
            </a>
        </div>
        <div class="btn-group me-3" role="group" aria-label="Ordina per">
            @php($currentOrder = strtolower(request("order", "datnum")))
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["order" => "datnum"])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentOrder === "datnum" ? "active" : "" }}"
            >
                Ordina per Striscia
            </a>
            <a
                href="{{ route("photos.stripes.index", array_merge(request()->except("page"), ["order" => "data"])) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentOrder === "data" ? "active" : "" }}"
            >
                Ordina per Data
            </a>
        </div>
        <div class="btn-group me-3" role="group" aria-label="Filtro data">
            @php($currentYear = request("year"))
            <a
                href="{{ route("photos.stripes.index", request()->except(["page", "year"])) }}"
                class="btn btn-sm btn-outline-secondary {{ empty($currentYear) ? "active" : "" }}"
            >
                Tutte le date
            </a>
        </div>
        @forelse ($years as $year)
            <a
                href="{{ route("photos.stripes.index", ["year" => $year->year, "name" => request("name"), "source" => request("source"), "order" => request("order", "datnum"), "no_photos" => request("no_photos"), "mismatch" => request("mismatch")]) }}"
                class="btn btn-sm btn-outline-secondary {{ $currentYear == $year->year ? "active" : "" }}"
            >
                {{ $year->year }}
                <span class="badge text-bg-secondary">
                    {{ $year->count }}
                </span>
            </a>
        @empty
            <p>Nessun anno</p>
        @endforelse
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
                                <span>
                                    <span class="text-muted">Data:</span>
                                    <span class="fw-semibold">
                                        {{ $stripe->data }}
                                    </span>
                                </span>
                                @if (! is_null($stripe->nfo))
                                    <span>
                                        <span class="text-muted">NFO:</span>
                                        <span class="fw-semibold">
                                            {{ $stripe->nfo }}
                                        </span>
                                    </span>
                                    @if ($photoCount == 0)
                                        <span class="badge text-bg-danger">
                                            Nessuna foto collegata
                                        </span>
                                    @elseif ($photoCount < $stripe->nfo)
                                        <span class="badge text-bg-warning">
                                            {{ $stripe->nfo - $photoCount }}
                                            foto mancanti
                                        </span>
                                    @elseif ($photoCount > $stripe->nfo)
                                        <span class="badge text-bg-danger">
                                            {{ $photoCount - $stripe->nfo }}
                                            foto in più
                                        </span>
                                    @endif
                                @endif

                                <span>
                                    <span class="text-muted">Strisce:</span>
                                    <span class="fw-semibold">
                                        {{ $stripe->datnum }}
                                        @if (! empty($stripe->anum) && $stripe->anum !== $stripe->datnum)
                                                — {{ $stripe->anum }}
                                        @endif
                                    </span>
                                </span>

                                <span class="badge text-bg-secondary">
                                    {{ $stripe->source }}
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
                            <a
                                href="{{ route("photos.stripes.show", $stripe->id) }}"
                                class="btn btn-sm btn-outline-secondary ms-auto"
                            >
                                Dettagli
                            </a>

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
                                                        data-src="{{ route("photos.preview", $photo->id) }}"
                                                        class="figure-img img-fluid rounded"
                                                        alt="{{ $photo->description }}"
                                                    />
                                                    <div
                                                        class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white"
                                                    >
                                                        <div class="small">
                                                            {{ $photo->file_name }}
                                                            {{ $photo->taken_at ? $photo->taken_at->format("Y-m-d") : "N/A" }}
                                                        </div>
                                                        <div class="small">
                                                            {{ $photo->subjects }}
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
    <script>
        document.addEventListener('show.bs.collapse', function (event) {
            var container = event.target;
            var imgs = container.querySelectorAll('img[data-src]');
            imgs.forEach(function (img) {
                if (!img.dataset.loaded) {
                    img.src = img.dataset.src;
                    img.dataset.loaded = '1';
                }
            });
        });
    </script>
@endsection
