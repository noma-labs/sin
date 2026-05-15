@extends("archiviodocumenti.layout")

@section("content")

    <div class="card border-0 bg-light mb-4">
        <div class="card-body">
            <p class="small text-muted mb-2">Documenti per anno</p>
            <form method="GET" action="{{ route("docs.index") }}">
                <div
                    class="d-flex gap-1"
                    style="height: 140px; align-items: flex-end"
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
                                class="small text-muted mb-1"
                                style="font-size: 0.65rem"
                            >
                                {{ $item->count }}
                            </span>
                            <div
                                class="rounded-top {{ $selectedYear == $item->decade ? "bg-warning" : "bg-primary" }}"
                                style="width: 100%; height: {{ $barHeight }}px"
                            ></div>
                            <span
                                class="small text-muted mt-1"
                                style="font-size: 0.65rem"
                            >
                                {{ $item->decade }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </form>
        </div>
    </div>

    @if ($selectedYear)
        <div class="mb-2">
            <a
                href="{{ route("docs.index") }}"
                class="btn btn-sm btn-outline-secondary"
            >
                Annulla filtro
            </a>
        </div>
        <div class="row g-2">
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm mb-2">
                    <div class="card-body">
                        @if ($selectedDocWords->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label">
                                    Parole frequenti nel titolo
                                </label>
                                <div>
                                    @foreach ($selectedDocWords as $word => $count)
                                        <a
                                            href="?year={{ $selectedYear }}@if($selectedMonth)&month={{ $selectedMonth }}@endif&q={{ urlencode($word) }}&doc={{ $selectedDocId }}"
                                            class="badge bg-info text-dark me-1 mb-1"
                                            style="
                                                cursor: pointer;
                                                text-decoration: none;
                                            "
                                        >
                                            {{ $word }}
                                            <span class="fw-normal">
                                                ({{ $count }})
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Mese</label>
                            <div class="d-flex flex-wrap gap-1">
                                <a
                                    href="?year={{ $selectedYear }}"
                                    class="badge {{ ! $selectedMonth ? "bg-primary" : "bg-secondary" }}"
                                    style="
                                        cursor: pointer;
                                        text-decoration: none;
                                    "
                                >
                                    Tutti ({{ $transcripts->count() }})
                                </a>
                                @foreach ($months as $num => $name)
                                    <a
                                        href="?year={{ $selectedYear }}&month={{ $num }}@if(request('q'))&q={{ urlencode(request('q')) }}@endif"
                                        class="badge {{ $selectedMonth == $num ? "bg-primary" : "bg-secondary" }}"
                                        style="
                                            cursor: pointer;
                                            text-decoration: none;
                                        "
                                    >
                                        {{ $name }}
                                        ({{ $countByMonth->get($num, 0) }})
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <form method="GET" action="{{ route("docs.index") }}">
                            <input
                                type="hidden"
                                name="year"
                                value="{{ $selectedYear }}"
                            />
                            <input
                                type="text"
                                name="q"
                                class="form-control form-control-sm mb-2"
                                placeholder="Cerca titolo o codice..."
                                value="{{ request("q") }}"
                            />
                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-primary w-100"
                            >
                                Cerca
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h2 class="text-bold mb-2">{{ $selectedYear }}</h2>
                <div class="row g-2">
                    @foreach ($transcripts as $doc)
                        @if ((! $selectedMonth || ($doc->recorded_date && \Carbon\Carbon::parse($doc->recorded_date)->month == $selectedMonth)) && (! request("q") || str_contains(strtolower($doc->title . " " . $doc->code), strtolower(request("q")))))
                            <div class="col-12 col-md-12 col-lg-12">
                                <a
                                    href="?year={{ $selectedYear }}@if($selectedMonth)&month={{ $selectedMonth }}@endif @if(request('q'))&q={{ urlencode(request('q')) }}@endif&doc={{ $doc->id }}"
                                    class="text-decoration-none"
                                >
                                    <div
                                        class="card h-100 border-0 shadow-sm {{ $selectedDocId == $doc->id ? "border-primary" : "" }}"
                                    >
                                        <div class="card-body py-2">
                                            <p
                                                class="small fw-semibold mb-1 text-truncate"
                                            >
                                            <span
                                                    class="badge text-bg-secondary"
                                                >
                                                    {{ $doc->recorded_date ? \Carbon\Carbon::parse($doc->recorded_date)->format("d/m/Y") : "Data sconosciuta" }}
                                                </span>
                                                {{ $doc->title }}
                                            </p>
                                            <p
                                                class="small text-muted mb-1"
                                                style="font-size: 0.75rem"
                                            >
                                                {{ $doc->code }}
                                            </p>
                                            <p class="mb-1">

                                            </p>
                                            <p
                                                class="small text-muted mb-0"
                                                style="
                                                    font-size: 0.75rem;
                                                    display: -webkit-box;
                                                    -webkit-line-clamp: 2;
                                                    -webkit-box-orient: vertical;
                                                    overflow: hidden;
                                                "
                                            >
                                                {{ $doc->description }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-md-5">
                @if ($selectedDoc)
                    <div class="card h-100 border-0 shadow">
                        <div
                            class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center"
                        >
                            <div>
                                <strong>{{ $selectedDoc->title }}</strong>
                                <span class="small ms-2">
                                    ({{ $selectedDoc->code }})
                                </span>
                            </div>
                            <div class="small mt-2 mt-md-0">
                                <span class="badge bg-light text-dark">
                                    {{ $selectedDoc->recorded_date ? \Carbon\Carbon::parse($selectedDoc->recorded_date)->format("d/m/Y") : "Data sconosciuta" }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="card-body overflow-y-auto"
                            style="max-height: 1024px"
                        >
                            <div
                                class="text-muted small"
                                style="line-height: 1.3; margin-bottom: 0.5rem"
                            >
                                {{ $selectedDoc->description }}
                            </div>
                            <div style="white-space: pre-line">
                                {{ $selectedDoc->content ?? "Nessun contenuto." }}
                            </div>
                        </div>
                    </div>
                @else
                    <div
                        class="card h-100 border-0 bg-light text-muted d-flex align-items-center justify-content-center"
                        style="min-height: 200px"
                    >
                        <div>
                            Seleziona un documento per vedere il contenuto
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
