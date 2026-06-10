@extends("archive.layout")

@section("content")
  <div class="row mb-3">
    <div class="col-md-4 offset-md-4 mt-3">
      <form method="GET" action="{{ route("archive.index") }}">
        @if ($selectedYear)
          <input type="hidden" name="year" value="{{ $selectedYear }}" />
        @endif

        @if ($selectedGenere)
          <input type="hidden" name="genere" value="{{ $selectedGenere }}" />
        @endif

        <div class="input-group">
          <input
            type="text"
            name="q"
            class="form-control"
            placeholder="Cerca nel testo..."
            value="{{ request("q") }}"
          />
          <button type="submit" class="btn btn-primary">Cerca</button>
          @if (request("q"))
            <a
              href="?{{ http_build_query(array_filter(["year" => $selectedYear, "genere" => $selectedGenere])) }}"
              class="btn btn-outline-secondary"
            >
              &times;
            </a>
          @endif

          @if ($selectedYear || $selectedGenere || request("q") || $selectedDocId)
            <a
              href="{{ route("archive.index") }}"
              class="btn btn-outline-secondary"
            >
              Reset filtri
            </a>
          @endif
        </div>
      </form>
    </div>
  </div>
  <div class="rounded-2 mb-3 px-3 pt-3 pb-1" style="background: #2c3e50">
    <form method="GET" action="{{ route("archive.index") }}">
      @if ($selectedGenere)
        <input type="hidden" name="genere" value="{{ $selectedGenere }}" />
      @endif

      @if (request("q"))
        <input type="hidden" name="q" value="{{ request("q") }}" />
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
      <div class="card border-0 bg-light mb-3">
        <div class="card-body py-2 px-2 d-flex align-items-center gap-2">
          <span class="text-muted small">Totale</span>
          <span class="fw-bold fs-6"> {{ number_format($totalCount) }} </span>
        </div>
      </div>

      @if ($genreOptions->isNotEmpty())
        <p
          class="small fw-bold text-uppercase text-muted mb-1 mt-3"
          style="font-size: 0.7rem; letter-spacing: 0.05em"
        >Genere</p>
        <div class="list-group list-group-flush mb-3">
          <a
            href="?{{ http_build_query(array_filter(["year" => $selectedYear, "q" => request("q")])) }}"
            class="list-group-item list-group-item-action py-1 px-2 {{ ! $selectedGenere ? "active" : "" }}"
            style="font-size: 0.8rem"
          >
            Tutti
          </a>
          @foreach ($genreOptions as $genere => $count)
            <a
              href="?{{ http_build_query(array_filter(["year" => $selectedYear, "q" => request("q"), "genere" => $genere])) }}"
              class="list-group-item list-group-item-action py-1 px-2 {{ $selectedGenere === $genere ? "active" : "" }}"
              style="font-size: 0.8rem"
            >
              {{ $genere }}
              <span class="float-end text-muted" style="font-size: 0.7rem">
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
      @foreach ($transcripts as $doc)
        @php
                    $params = array_filter(
                        [
                            "year" => $selectedYear,
                            "genere" => $selectedGenere,
                            "q" => request("q"),
                            "doc" => $doc->id,
                        ],
                        fn ($value) => $value !== null && $value !== "",
                    );
                    $docUrl = "?" . http_build_query($params);
                @endphp
        <a href="{{ $docUrl }}" class="text-decoration-none">
          <div
            class="card border-0 shadow-sm mb-2 {{ $selectedDocId == $doc->id ? "border-start border-primary border-3" : "" }}"
          >
            <div class="card-body py-2">
              <div
                class="d-flex justify-content-between align-items-start mb-1 gap-2"
              >
                <p
                  class="fw-semibold mb-0 text-truncate"
                  style="font-size: 0.85rem"
                >
                  {{ $doc->argomento }}
                </p>
                @if (! $doc->transcript)
                  <span
                    class="badge bg-danger text-white flex-shrink-0"
                    style="font-size: 0.6rem; white-space: nowrap"
                  >
                    ⚠ Senza trascrizione213
                  </span>
                @endif
              </div>
              <p class="text-muted mb-1" style="font-size: 0.75rem">
                {{ $doc->data ? \Carbon\Carbon::parse($doc->data)->format("d/m/Y") : "Data sconosciuta" }}
                @if ($doc->code)
                  &middot; {{ $doc->code }}
                @endif

                @if ($doc->AUTORE)
                  &middot; {{ $doc->AUTORE }}
                @endif

                @if ($doc->LOCALITA)
                  &middot; {{ $doc->LOCALITA }}
                @endif
              </p>
              @if (isset($doc->relevance) && $doc->relevance !== null)
                <span
                  class="badge bg-primary bg-opacity-10 text-primary"
                  style="font-size: 0.65rem"
                >
                  rilevanza {{ number_format((float) $doc->relevance, 2) }}
                </span>
              @endif
            </div>
          </div>
        </a>
      @endforeach
    </div>

    <div class="col-md-6">
      @if ($selectedDoc)
        <div
          class="card border-0 shadow"
          style="max-height: calc(100vh - 260px); overflow-y: auto"
        >
          <div class="card-header bg-white border-bottom py-3 px-3">
            <h2 class="h6 fw-bold mb-2">{{ $selectedDoc->argomento }}</h2>
            <p class="text-muted mb-2" style="font-size: 0.8rem">
              {{ $selectedDoc->data ? \Carbon\Carbon::parse($selectedDoc->data)->format("d/m/Y") : "Data sconosciuta" }}
              @if ($selectedDoc->code)
                &middot; {{ $selectedDoc->code }}
              @endif
            </p>
            @if ($selectedDoc->AUTORE || $selectedDoc->DESTINATARI || $selectedDoc->LOCALITA)
              <p
                class="text-muted mb-0 text-truncate"
                style="font-size: 0.74rem"
              >
                @if ($selectedDoc->AUTORE)
                  <span
                    class="text-uppercase"
                    style="font-size: 0.62rem; letter-spacing: 0.05em"
                  >
                    Autore
                  </span>
                  :
                  {{ $selectedDoc->AUTORE }}
                @endif

                @if ($selectedDoc->DESTINATARI)
                  @if ($selectedDoc->AUTORE)
                    &middot;
                  @endif
                  <span
                    class="text-uppercase"
                    style="font-size: 0.62rem; letter-spacing: 0.05em"
                  >
                    Destinatari
                  </span>
                  :
                  {{ $selectedDoc->DESTINATARI }}
                @endif

                @if ($selectedDoc->LOCALITA)
                  @if ($selectedDoc->AUTORE || $selectedDoc->DESTINATARI)
                    &middot;
                  @endif
                  <span
                    class="text-uppercase"
                    style="font-size: 0.62rem; letter-spacing: 0.05em"
                  >
                    Localita
                  </span>
                  :
                  {{ $selectedDoc->LOCALITA }}
                @endif
              </p>
            @endif
          </div>
          <div class="card-body p-2">
            @if ($selectedDoc->audio)
              <audio
                controls
                class="w-100 mb-3"
                style="height: 36px"
                src="{{ route('archive.audio', $selectedDoc->audio->id) }}"
              ></audio>
            @endif
            @if ($selectedDoc->transcript)
              <div class="bg-light rounded-2 p-3 border">
                <div
                  style="
                    white-space: pre-wrap;
                    font-size: 0.95rem;
                    line-height: 1.75;
                    color: #1f2937;
                  "
                >
                  {{ $selectedDoc->transcript->content }}
                </div>
              </div>
            @else
              <p
                class="text-muted mb-0"
                style="font-size: 0.85rem"
              >Nessuna trascrizione disponibile.</p>
            @endif
          </div>
        </div>
      @else
        <div
          class="card border-0 bg-light text-muted d-flex align-items-center justify-content-center"
          style="min-height: 200px; max-height: calc(100vh - 260px)"
        >
          <div class="card-body text-center">
            <p
              class="mb-0"
              style="font-size: 0.85rem"
            >Seleziona un documento per vedere il contenuto</p>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection
