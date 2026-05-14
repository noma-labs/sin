@extends("archiviodocumenti.layout")

@section("content")
    @include("partials.header", ["title" => "Documenti"])

    @php
        $maxCount = $countByDecade->max("count") ?: 1;
        $selectedYear = request("year");
        $selectedDocId = request("doc");
        $selectedMonth = request("month");
        $selectedDoc = null;
        if ($selectedDocId && isset($transcripts)) {
            $selectedDoc = $transcripts->firstWhere("id", $selectedDocId);
        }
        $months = [
            1 => "Gennaio",
            2 => "Febbraio",
            3 => "Marzo",
            4 => "Aprile",
            5 => "Maggio",
            6 => "Giugno",
            7 => "Luglio",
            8 => "Agosto",
            9 => "Settembre",
            10 => "Ottobre",
            11 => "Novembre",
            12 => "Dicembre",
        ];
    @endphp

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
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Documenti del {{ $selectedYear }}</h6>
            <span class="badge bg-secondary">
                {{ $transcripts->count() }} documenti
            </span>
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
                        <form method="GET" action="{{ route("docs.index") }}">
                            <input
                                type="hidden"
                                name="year"
                                value="{{ $selectedYear }}"
                            />
                            <div class="mb-3">
                                <label for="month" class="form-label">
                                    Mese
                                </label>
                                <select
                                    name="month"
                                    id="month"
                                    class="form-select form-select-sm"
                                    onchange="this.form.submit()"
                                >
                                    <option value="">Tutti</option>
                                    @foreach ($months as $num => $name)
                                        <option
                                            value="{{ $num }}"
                                            @if($selectedMonth == $num) selected @endif
                                        >
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
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
                                                {{ $doc->title }}
                                            </p>
                                            <p
                                                class="small text-muted mb-1"
                                                style="font-size: 0.75rem"
                                            >
                                                {{ $doc->code }}
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
                            class="card-body"
                            style="
                                white-space: pre-line;
                                max-height: 480px;
                                overflow-y: auto;
                            "
                        >
                            <div class="mb-2 text-muted small">
                                {{ $selectedDoc->description }}
                            </div>
                            <div>
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
