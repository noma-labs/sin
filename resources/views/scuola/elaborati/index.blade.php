@extends("scuola.index")

@section("content")
  @php
    $link = fn (array $params = []) => route(
      "scuola.elaborati.index",
      array_filter(
        array_merge(["year" => request("year"), "order" => $order, "view" => $view, "q" => $search], $params),
        fn ($value) => $value !== null && $value !== "",
      ),
    );
    $classi = fn ($elaborato) => str_replace(",", ", ", (string) $elaborato->classi);
  @endphp

  <div class="elaborati-summary">
    <div
      class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3"
    >
      <div>
        <h1 class="h3 mb-1">Elaborati</h1>
        <p class="text-body-secondary mb-0">
          {{ $total }} elaborati in archivio
        </p>
      </div>
      <a
        class="btn btn-primary d-inline-flex align-items-center gap-2"
        href="{{ route("scuola.elaborati.create") }}"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
        Inserisci elaborato
      </a>
    </div>

    <form
      method="GET"
      action="{{ route("scuola.elaborati.index") }}"
      class="d-flex flex-wrap align-items-center gap-2 pb-3 mb-4 border-bottom"
    >
      <input type="hidden" name="year" value="{{ request("year") }}" />
      <input type="hidden" name="view" value="{{ $view }}" />
      <div class="input-group flex-grow-1" style="flex-basis: 320px">
        <span class="input-group-text bg-body">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.5-3.5" />
          </svg>
        </span>
        <label for="q" class="visually-hidden">Cerca elaborati</label>
        <input
          id="q"
          name="q"
          type="search"
          class="form-control"
          value="{{ $search }}"
          placeholder="Cerca per titolo o collocazione"
        />
      </div>
      <label for="order" class="text-body-secondary small">Ordina per</label>
      <select
        id="order"
        name="order"
        class="form-select w-auto"
        onchange="this.form.submit()"
      >
        <option value="anno_scolastico" @selected($order === "anno_scolastico")>
          Anno scolastico
        </option>
        <option value="collocazione" @selected($order === "collocazione")>
          Collocazione
        </option>
      </select>
      <nav class="nav elaborati-vista" aria-label="Vista">
        <a
          class="nav-link {{ $view !== "table" ? "active" : "" }}"
          @if ($view !== "table") aria-current="page" @endif
          href="{{ $link(["view" => "cards"]) }}"
        >
          Griglia
        </a>
        <a
          class="nav-link {{ $view === "table" ? "active" : "" }}"
          @if ($view === "table") aria-current="page" @endif
          href="{{ $link(["view" => "table"]) }}"
        >
          Tabella
        </a>
      </nav>
    </form>

    <div class="row g-4">
      <aside class="col-12 col-lg-3 col-xl-2">
        <h2 class="elaborati-label">Anno scolastico</h2>
        <div class="list-group elaborati-anni mb-4">
          <a
            href="{{ $link(["year" => null]) }}"
            class="list-group-item list-group-item-action d-flex justify-content-between {{ request("year") ? "" : "active" }}"
          >
            Tutti
            <span>{{ $total }}</span>
          </a>
          @foreach ($years as $year)
            <a
              href="{{ $link(["year" => $year->year]) }}"
              class="list-group-item list-group-item-action d-flex justify-content-between {{ request("year") == $year->year ? "active" : "" }}"
            >
              {{ $year->year }}
              <span class="text-body-secondary">{{ $year->count }}</span>
            </a>
          @endforeach
        </div>

        <h2 class="elaborati-label">Ciclo</h2>
        <ul class="list-unstyled small d-flex flex-column gap-2 ps-2">
          <li class="d-flex align-items-center gap-2">
            <span class="elaborati-swatch ciclo-prescuola"></span>
            Prescuola
          </li>
          <li class="d-flex align-items-center gap-2">
            <span class="elaborati-swatch ciclo-elementari"></span>
            Elementari
          </li>
          <li class="d-flex align-items-center gap-2">
            <span class="elaborati-swatch ciclo-medie"></span>
            Medie
          </li>
          <li class="d-flex align-items-center gap-2">
            <span class="elaborati-swatch ciclo-superiori"></span>
            Superiori
          </li>
          <li class="d-flex align-items-center gap-2">
            <span class="elaborati-swatch ciclo-tutti"></span>
            Più cicli
          </li>
        </ul>
      </aside>

      <main class="col">
        @if ($count === 0)
          <p class="text-body-secondary">Nessun elaborato trovato.</p>
        @elseif ($view === "table")
          <div class="table-responsive border rounded-3">
            <table class="table table-hover align-middle mb-0 elaborati-table">
              <thead>
                <tr>
                  <th scope="col">Collocazione</th>
                  <th scope="col">Titolo</th>
                  <th scope="col">Classi</th>
                  <th scope="col">Rilegatura</th>
                  <th scope="col">File</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($gruppi as $anno => $elaborati)
                  @if ($anno !== "")
                    <tr class="elaborati-anno-row">
                      <th colspan="5" scope="rowgroup">
                        {{ $anno }}
                        <span class="small fw-normal text-body-secondary ms-2">
                          {{ trans_choice("{1} :count elaborato|[2,*] :count elaborati", $elaborati->count()) }}
                        </span>
                      </th>
                    </tr>
                  @endif

                  @foreach ($elaborati as $elaborato)
                    <tr>
                      <td class="font-monospace fw-semibold">
                        {{ $elaborato->collocazione }}
                      </td>
                      <td>
                        <a
                          href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                          class="fw-semibold link-body-emphasis link-underline-opacity-0 link-underline-opacity-100-hover"
                        >
                          {{ $elaborato->titolo }}
                        </a>
                      </td>
                      <td>
                        @if ($elaborato->classi)
                          <span
                            class="badge rounded-pill elaborato-ciclo ciclo-{{ $elaborato->ciclo() }}"
                          >
                            {{ $classi($elaborato) }}
                          </span>
                        @endif
                      </td>
                      <td class="text-body-secondary">
                        {{ $elaborato->rilegatura }}
                      </td>
                      <td class="text-nowrap">
                        @if ($elaborato->file_path)
                          <a
                            href="{{ route("scuola.elaborati.preview", $elaborato->id) }}"
                          >
                            PDF
                          </a>
                          <span class="text-body-secondary">
                            {{ Illuminate\Support\Number::fileSize($elaborato->file_size) }}
                          </span>
                        @else
                          <span class="text-body-secondary">
                            Solo cartaceo
                          </span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          @foreach ($gruppi as $anno => $elaborati)
            <section class="mb-4">
              @if ($anno !== "")
                <div class="d-flex align-items-center gap-2 mb-3">
                  <h2 class="h5 mb-0">{{ $anno }}</h2>
                  <span class="small text-body-secondary">
                    {{ trans_choice("{1} :count elaborato|[2,*] :count elaborati", $elaborati->count()) }}
                  </span>
                  <span class="flex-grow-1 border-top"></span>
                </div>
              @endif

              <div class="row row-cols-2 row-cols-md-3 row-cols-xxl-4 g-3">
                @foreach ($elaborati as $elaborato)
                  <div class="col">
                    <a
                      href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                      class="card h-100 elaborato-card text-decoration-none"
                    >
                      @if ($elaborato->cover_image_path)
                        <img
                          src="{{ route("scuola.elaborati.cover.show", $elaborato->id) }}"
                          class="card-img-top elaborato-cover-img"
                          alt=""
                        />
                      @else
                        <div
                          class="elaborato-cover ciclo-{{ $elaborato->ciclo() }}"
                        >
                          <span class="elaborato-cover-ciclo">
                            {{ $elaborato->cicloLabel() }}
                          </span>
                          <span class="elaborato-cover-collocazione">
                            {{ $elaborato->collocazione }}
                          </span>
                        </div>
                      @endif
                      <div class="card-body d-flex flex-column gap-1">
                        <h3 class="h6 card-title mb-0">
                          {{ $elaborato->titolo }}
                        </h3>
                        <p class="small text-body-secondary mb-0">
                          {{ collect([$classi($elaborato), $elaborato->rilegatura])->filter()->implode(" · ") }}
                        </p>
                        <p
                          class="small text-body mt-auto pt-2 mb-0 d-flex align-items-center gap-1"
                        >
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8z" />
                            <path d="M14 3v5h5" />
                          </svg>
                          @if ($elaborato->file_path)
                            PDF ·
                            {{ Illuminate\Support\Number::fileSize($elaborato->file_size) }}
                          @else
                            Solo cartaceo
                          @endif
                        </p>
                      </div>
                    </a>
                  </div>
                @endforeach
              </div>
            </section>
          @endforeach
        @endif
      </main>
    </div>
  </div>
@endsection
