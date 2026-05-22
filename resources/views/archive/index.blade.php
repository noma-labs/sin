@extends("archive.layout")

@section("content")
    <div class="d-flex align-items-center justify-content-end mb-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-2">
                <span class="text-muted small">Registrazioni</span>
                <span class="fw-bold fs-5">
                    {{ number_format($totalCount) }}
                </span>
            </div>
        </div>
    </div>
    <div class="rounded-2 mb-3 px-3 pt-3 pb-1" style="background: #2c3e50">
        <form method="GET" action="{{ route("archive.index") }}">
            @if ($selectedMonth)
                <input
                    type="hidden"
                    name="month"
                    value="{{ $selectedMonth }}"
                />
            @endif

            <div
                class="d-flex gap-1"
                style="height: 140px; align-items: flex-end; padding-top: 20px"
            >
                @foreach ($countByDecade as $item)
                    @php
                        $barHeight = (int) round(($item->count / $maxCount) * 100);
                    @endphp

                    <button
                        type="submit"
                        name="year"
                        value="{{ $item->decade }}"
                        class="border-0 bg-transparent p-0 d-flex flex-column align-items-center"
                        style="flex: 1; cursor: pointer; outline: none"
                    >
                        <span
                            class="text-light mb-1"
                            style="font-size: 0.65rem; font-weight: 500"
                        >
                            {{ $item->count }}
                        </span>
                        <div
                            class="rounded-top {{ $selectedYear == $item->decade ? "bg-warning" : "bg-secondary" }}"
                            style="
                                width: 100%;
                                height: {{ $barHeight }}px;
                                opacity: 0.85;
                            "
                        ></div>
                        <span
                            class="text-light mt-1"
                            style="font-size: 0.6rem; white-space: nowrap"
                        >
                            {{ $item->decade }}
                        </span>
                    </button>
                @endforeach
            </div>
        </form>
    </div>

    <div class="row g-2" style="min-height: 70vh">
        <div
            class="col-md-2"
            style="max-height: calc(100vh - 260px); overflow-y: auto"
        >
            {{-- Top words --}}
            @if ($selectedDocWords->isNotEmpty())
                <p
                    class="small fw-bold text-uppercase text-muted mb-1"
                    style="font-size: 0.7rem; letter-spacing: 0.05em"
                >
                    Argomento
                </p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach ($selectedDocWords as $word => $count)
                        <a
                            href="?{{ http_build_query(array_filter(["year" => $selectedYear, "month" => $selectedMonth, "q" => $word])) }}"
                            class="badge bg-secondary text-decoration-none d-inline-flex align-items-center gap-1"
                            style="font-size: 0.7rem; font-weight: normal"
                        >
                            {{ $word }}
                            <span class="badge bg-light text-dark" style="font-size: 0.65rem">{{ $count }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            <p
                class="small fw-bold text-uppercase text-muted mb-1"
                style="font-size: 0.7rem; letter-spacing: 0.05em"
            >
                Mese
            </p>
            <div class="list-group list-group-flush mb-3">
                <a
                    href="?{{ http_build_query(array_filter(["year" => $selectedYear, "q" => request("q"), "genere" => $selectedGenere])) }}"
                    class="list-group-item list-group-item-action py-1 px-2 {{ ! $selectedMonth ? "active" : "" }}"
                    style="font-size: 0.8rem"
                >
                    Tutti
                    <span
                        class="float-end text-muted"
                        style="font-size: 0.7rem"
                    >
                        {{ $transcripts->count() }}
                    </span>
                </a>
                @foreach ($months as $num => $name)
                    @if ($countByMonth->get($num, 0) > 0)
                        <a
                            href="?{{ http_build_query(array_filter(["year" => $selectedYear, "month" => $num, "q" => request("q"), "genere" => $selectedGenere])) }}"
                            class="list-group-item list-group-item-action py-1 px-2 {{ $selectedMonth == $num ? "active" : "" }}"
                            style="font-size: 0.8rem"
                        >
                            {{ $name }}
                            <span
                                class="float-end text-muted"
                                style="font-size: 0.7rem"
                            >
                                {{ $countByMonth->get($num, 0) }}
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Genere --}}
            @if ($genreOptions->isNotEmpty())
                <p
                    class="small fw-bold text-uppercase text-muted mb-1 mt-3"
                    style="font-size: 0.7rem; letter-spacing: 0.05em"
                >
                    Genere
                </p>
                <div class="list-group list-group-flush mb-3">
                    <a
                        href="?{{ http_build_query(array_filter(["year" => $selectedYear, "month" => $selectedMonth, "q" => request("q")])) }}"
                        class="list-group-item list-group-item-action py-1 px-2 {{ ! $selectedGenere ? "active" : "" }}"
                        style="font-size: 0.8rem"
                    >
                        Tutti
                    </a>
                    @foreach ($genreOptions as $genere => $count)
                        <a
                            href="?{{ http_build_query(array_filter(["year" => $selectedYear, "month" => $selectedMonth, "q" => request("q"), "genere" => $genere])) }}"
                            class="list-group-item list-group-item-action py-1 px-2 {{ $selectedGenere === $genere ? "active" : "" }}"
                            style="font-size: 0.8rem"
                        >
                            {{ $genere }}
                            <span
                                class="float-end text-muted"
                                style="font-size: 0.7rem"
                            >
                                {{ $count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div
            class="col-md-4"
            style="max-height: calc(100vh - 260px); overflow-y: auto"
        >
            <div
                class="pb-2 mb-2 border-bottom bg-white"
                style="position: sticky; top: 0; z-index: 10"
            >
                <form method="GET" action="{{ route("archive.index") }}">
                    @if ($selectedYear)
                        <input
                            type="hidden"
                            name="year"
                            value="{{ $selectedYear }}"
                        />
                    @endif

                    @if ($selectedMonth)
                        <input
                            type="hidden"
                            name="month"
                            value="{{ $selectedMonth }}"
                        />
                    @endif

                    <div class="d-flex align-items-center gap-2">
                        @if ($selectedYear)
                            <h5 class="fw-bold mb-0 me-auto">
                                {{ $selectedYear }}
                            </h5>
                        @else
                            <h5
                                class="fw-bold mb-0 me-auto text-muted"
                                style="font-size: 0.9rem"
                            >
                                Tutte le registrazioni
                            </h5>
                        @endif
                        <div
                            class="input-group input-group-sm"
                            style="max-width: 220px"
                        >
                            <input
                                type="text"
                                name="q"
                                class="form-control form-control-sm"
                                placeholder="Cerca titolo o codice..."
                                value="{{ request("q") }}"
                            />
                            @if (request("q"))
                                <a
                                    href="?{{ http_build_query(array_filter(["year" => $selectedYear, "month" => $selectedMonth])) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    &times;
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            @foreach ($transcripts as $doc)
                @if (

                    ! request("q") ||
                    str_contains(
                        strtolower($doc->argomento . " " . $doc->code),
                        strtolower(request("q"))
                    )                )
                    @php
                        $params = array_filter([
                            "year" => $selectedYear,
                            "month" => $selectedMonth,
                            "q" => request("q"),
                            "doc" => $doc->id,
                        ]);
                        $docUrl = "?" . http_build_query($params);
                    @endphp

                    <a href="{{ $docUrl }}" class="text-decoration-none">
                        <div
                            class="card border-0 shadow-sm mb-2 {{ $selectedDocId == $doc->id ? "border-start border-primary border-3" : "" }}"
                        >
                            <div class="card-body py-2">
                                <p
                                    class="fw-semibold mb-1 text-truncate"
                                    style="font-size: 0.85rem"
                                >
                                    {{ $doc->argomento }}
                                </p>
                                <p
                                    class="text-muted mb-1"
                                    style="font-size: 0.75rem"
                                >
                                    {{ $doc->data ? \Carbon\Carbon::parse($doc->data)->format("d/m/Y") : "Data sconosciuta" }}
                                    @if ($doc->code)
                                            &middot; {{ $doc->code }}
                                    @endif

                                    @if ($doc->AUTORE)
                                            &middot; {{ $doc->AUTORE }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>

        <div class="col-md-6">
            @if ($selectedDoc)
                <div
                    class="card border-0 shadow"
                    style="max-height: calc(100vh - 260px); overflow-y: auto"
                >
                    <div class="card-header bg-white border-bottom py-2">
                        <p class="fw-bold mb-0" style="font-size: 0.9rem">
                            {{ $selectedDoc->argomento }}
                        </p>
                        <p class="text-muted mb-0" style="font-size: 0.75rem">
                            {{ $selectedDoc->data ? \Carbon\Carbon::parse($selectedDoc->data)->format("d/m/Y") : "Data sconosciuta" }}
                            @if ($selectedDoc->code)
                                    &middot; {{ $selectedDoc->code }}
                            @endif
                        </p>
                        @if ($selectedDoc->AUTORE)
                            <p
                                class="text-muted mb-0"
                                style="font-size: 0.75rem"
                            >
                                <span
                                    class="text-uppercase"
                                    style="
                                        font-size: 0.65rem;
                                        letter-spacing: 0.05em;
                                    "
                                >
                                    Autore
                                </span>
                                &middot; {{ $selectedDoc->AUTORE }}
                            </p>
                        @endif

                        @if ($selectedDoc->DESTINATARI)
                            <p
                                class="text-muted mb-0"
                                style="font-size: 0.75rem"
                            >
                                <span
                                    class="text-uppercase"
                                    style="
                                        font-size: 0.65rem;
                                        letter-spacing: 0.05em;
                                    "
                                >
                                    Destinatari
                                </span>
                                &middot; {{ $selectedDoc->DESTINATARI }}
                            </p>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($selectedDoc->transcript)
                            <div
                                style="
                                    white-space: pre-line;
                                    font-size: 0.875rem;
                                    line-height: 1.6;
                                "
                            >
                                {{ $selectedDoc->transcript->content }}
                            </div>
                        @else
                            <p
                                class="text-muted mb-0"
                                style="font-size: 0.85rem"
                            >
                                Nessuna trascrizione disponibile.
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <div
                    class="card border-0 bg-light text-muted d-flex align-items-center justify-content-center"
                    style="min-height: 200px; max-height: calc(100vh - 260px)"
                >
                    <div class="card-body text-center">
                        <p class="mb-0" style="font-size: 0.85rem">
                            Seleziona un documento per vedere il contenuto
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
