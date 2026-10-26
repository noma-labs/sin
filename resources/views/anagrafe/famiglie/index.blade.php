@extends("nomadelfia.index")

@section("content")
  @include("partials.header", ["title" => "Gestione Famiglie"])
  @can("popolazione.persona.inserisci")
    <div class="d-flex justify-content-end mb-3">
      <a class="btn btn-success" href="{{ route("anagrafe.families.create") }}">
        Nuova famiglia
      </a>
    </div>
  @endcan
  <div class="row row-cols-1 row-cols-md-4 d-flex">
    <div class="col">
      <div id="accordion">
        <div class="card">
          <div class="card-header" id="headSingle">
            <h5 class="mb-0">
              <button
                class="btn btn-link"
                data-bs-toggle="collapse"
                data-bs-target="#Single"
                aria-expanded="true"
                aria-controls="Single"
              >
                Famiglie
              </button>
            </h5>
          </div>
          <div
            id="Single"
            class="show"
            aria-labelledby="headSingle"
            data-parent="#accordion"
          >
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h5>
                    Uomini
                    <span class="badge bg-primary rounded-pill">
                      {{ $capifamiglieMaschio->count() }}
                    </span>
                  </h5>

                  @foreach ($capifamiglieMaschio->get() as $uomo)
                    <div>
                      <a
                        href="{{ route("anagrafe.families.show", $uomo->id) }}"
                      >
                        {{ $uomo->nome_famiglia }}
                      </a>
                      @if ($interni->contains($uomo->persona_id))
                        <span class="badge bg-success">In Nomadelfia</span>
                      @endif
                    </div>
                  @endforeach
                </div>
                <div class="col-md-6">
                  <h5>
                    Donne
                    <span class="badge bg-primary rounded-pill">
                      {{ $capifamiglieFemmina->count() }}
                    </span>
                  </h5>
                  @foreach ($capifamiglieFemmina->get() as $donna)
                    <div>
                      <a
                        href="{{ route("anagrafe.families.show", $donna->id) }}"
                      >
                        {{ $donna->nome_famiglia }}
                      </a>
                      @if ($interni->contains($donna->persona_id))
                        <span class="badge bg-success">In Nomadelfia</span>
                      @endif
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col">
      <div id="accordion">
        <div class="card">
          <div class="card-header" id="headCapoFamiglia">
            <h5 class="mb-0">
              <button
                class="btn btn-link"
                data-bs-toggle="collapse"
                data-bs-target="#CapoFamiglia"
                aria-expanded="true"
                aria-controls="CapoFamiglia"
              >
                Single
              </button>
              @if ($tuttiSingle)
                <a
                  class="btn btn-sm btn-outline-secondary"
                  href="{{ route("anagrafe.families") }}"
                >
                  Solo in Nomadelfia
                </a>
              @else
                <a
                  class="btn btn-sm btn-outline-secondary"
                  href="{{ route("anagrafe.families", ["single" => "tutti"]) }}"
                >
                  Mostra tutti
                </a>
              @endif
            </h5>
          </div>
          <div
            id="CapoFamiglia"
            class="show"
            aria-labelledby="headCapoFamiglia"
            data-parent="#accordion"
          >
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h5>
                    Uomini
                    <span class="badge bg-primary rounded-pill">
                      {{ $singleMaschio->count() }}
                    </span>
                  </h5>

                  @foreach ($singleMaschio->get() as $uomo)
                    <div>
                      <a
                        href="{{ route("nomadelfia.person.show", ["id" => $uomo->id]) }}"
                      >
                        {{ $uomo->nome_famiglia }}
                      </a>
                      @if ($interni->contains($uomo->persona_id))
                        <span class="badge bg-success">In Nomadelfia</span>
                      @endif
                    </div>
                  @endforeach
                </div>
                <div class="col-md-6">
                  <h5>
                    Donne
                    <span class="badge bg-primary rounded-pill">
                      {{ $singleFemmine->count() }}
                    </span>
                  </h5>
                  @foreach ($singleFemmine->get() as $donna)
                    <div>
                      <a
                        href="{{ route("nomadelfia.person.show", ["id" => $donna->id]) }}"
                      >
                        {{ $donna->nome_famiglia }}
                      </a>
                      @if ($interni->contains($donna->persona_id))
                        <span class="badge bg-success">In Nomadelfia</span>
                      @endif
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col">
      <div id="accordion">
        <div class="card">
          <div class="card-header" id="headCapoFamiglia">
            <h5 class="mb-0">
              <button
                class="btn btn-link"
                data-bs-toggle="collapse"
                data-bs-target="#Errori"
                aria-expanded="true"
                aria-controls="Errori"
              >
                <span class="text text-danger">
                  Risolvi i seguenti errori
                </span>
              </button>
            </h5>
          </div>
          <div
            id="Errori"
            class="show"
            aria-labelledby="headErrori"
            data-parent="#accordion"
          >
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  @foreach ($famigliaError as $famigliaWithDescr)
                    <
                    <span class="text text-danger">
                      {{ $famigliaWithDescr->descrizione }} ({{ count($famigliaWithDescr->results) }})
                    </span>
                    <ul>
                      @foreach ($famigliaWithDescr->results as $famiglia)
                        <li>
                          <a
                            href="{{ route("anagrafe.families.show", $famiglia->id) }}"
                          >
                            {{ $famiglia->nome_famiglia }}
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
