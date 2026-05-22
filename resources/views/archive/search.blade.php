@extends("archive.layout")

@section("title", "Ricerca")

@section("content")
    <div class="container-fluid my-2">
        <div class="row" style="height: calc(100vh - 200px)">
            <!-- Master Panel (Left) -->
            <div class="col-md-2 border-end">
                <form
                    method="GET"
                    action="{{ route("archive.search") }}"
                    class="mb-4"
                >
                    <div class="input-group">
                        <input
                            type="text"
                            name="q"
                            class="form-control"
                            placeholder="Inserisci termine di ricerca..."
                            value="{{ $term }}"
                            autocomplete="off"
                        />
                        <button class="btn btn-primary" type="submit">
                            Cerca
                        </button>
                    </div>
                </form>

                @if (! empty($term))
                    <div class="alert alert-info">
                        Trovati
                        <strong>{{ count($results) }}</strong>
                        risultato(i) per "
                        <strong>{{ $term }}</strong>
                        "
                    </div>

                    @if (count($results) > 0)
                        <div
                            style="
                                max-height: calc(100vh - 300px);
                                overflow-y: auto;
                            "
                        >
                            @foreach ($results as $transcript)
                                <a
                                    href="{{ route("archive.search", ["q" => $term, "selected" => $transcript->id]) }}"
                                    class="card mb-2 border text-decoration-none {{ request("selected") == $transcript->id ? "border-primary bg-light" : "" }}"
                                    style="
                                        cursor: pointer;
                                        transition: all 0.2s;
                                    "
                                >
                                    <div class="card-body py-2">
                                        <div
                                            class="d-flex justify-content-between align-items-start mb-1"
                                        >
                                            <div>
                                                <h6
                                                    class="card-title mb-1 text-dark"
                                                >
                                                    {{ $transcript->title }}
                                                    @if ($transcript->recorded_date)
                                                        <span
                                                            class="badge bg-secondary"
                                                        >
                                                            {{ $transcript->recorded_date->format("d M Y") }}
                                                        </span>
                                                    @endif
                                                </h6>
                                            </div>
                                            <span class="badge bg-success">
                                                {{ number_format($transcript->relevance, 2) }}
                                            </span>
                                        </div>
                                        {{-- <span class="badge bg-secondary">{{ $transcript->code }}</span> --}}
                                        <p
                                            class="card-text small text-muted mt-2 mb-0"
                                        >
                                            {{ Str::limit($transcript->description, 80) }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Nessuna trascrizione trovata corrispondente alla tua
                            ricerca.
                        </div>
                    @endif
                @else
                    <div class="alert alert-secondary">
                        Inserisci un termine di ricerca per trovare trascrizioni
                        per contenuto.
                    </div>
                @endif
            </div>

            <!-- Detail Panel (Right) -->
            <div class="col-md-10 ps-4">
                @if (! empty($term) && request("selected"))
                    @php
                        $selected = $results->firstWhere("id", request("selected"));
                    @endphp

                    @if ($selected)
                        <div
                            style="
                                max-height: calc(100vh - 200px);
                                overflow-y: auto;
                            "
                        >
                            <h2 class="mb-3">{{ $selected->title }}</h2>
                            <span class="badge bg-primary mb-2">
                                {{ $selected->code }}
                            </span>

                            @if ($selected->recorded_date)
                                <span class="badge bg-secondary mb-2">
                                    {{ $selected->recorded_date->format("d M Y") }}
                                </span>
                            @endif

                            @if ($selected->description)
                                <div
                                    class="bg-light border-start border-5 border-primary ps-2 py-2 mb-3"
                                >
                                    <p class="mb-0 text-dark">
                                        <em>{{ $selected->description }}</em>
                                    </p>
                                </div>
                            @endif

                            @if ($selected->content)
                                <div
                                    class="content-body"
                                    style="line-height: 1.8; color: #333"
                                >
                                    @foreach (explode("\n", $selected->content) as $line)
                                        <p class="mb-2">
                                            {!!
                                                preg_replace(
                                                    "/(" . preg_quote($term, "/") . ")/i",
                                                    '<mark class="bg-warning text-dark fw-bold">$1</mark>',
                                                    e($line),
                                                )
                                            !!}
                                        </p>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No content available.</p>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted mt-5">
                        <p>
                            Seleziona una trascrizione per visualizzare i
                            dettagli
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        a.card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
@endsection
