@extends("nomadelfia.persone.index")

@section("content")
  @include("partials.header", ["title" => "Gestione Entrate/Uscite: " . $persona->nominativo])
  <div class="row justify-content-center">
    <div class="col-md-6">
      @if ($persona->isPersonaInterna())
        <div class="card">
          <div class="card-header">Posizione attuale</div>
          <div class="card-body">
            <div class="row">
              <p class="col-md-4 fw-bold">Data Entrata</p>
              <p class="col-md-2 fw-bold">Tempo trascorso</p>
              <p class="col-md-6 fw-bold">Operazioni</p>
            </div>
            <div class="row">
              <p class="col-md-4">{{ $attuale->data_entrata }}</p>
              <div class="col-md-2">
                <span class="badge text-bg-info">
                  {{ Carbon::parse($attuale->data_entrata)->diffForHumans(["short" => true]) }}
                </span>
              </div>
              <div class="col-md-6">
                @include(
                                    "nomadelfia.templates.modificaDataEntrata",
                                    ["persona" => $persona, "data_entrata" => $attuale->data_entrata]
                                )
              </div>
            </div>
          </div>
        </div>
      @endif

      <div class="card my-3">
        <div
          class="card-header d-flex justify-content-between align-items-center"
        >
          Storico Entrate/Uscite
          @can("popolazione.persona.inserisci")
            <x-modal
              modal-title="Aggiungi periodo storico"
              button-title="Aggiungi periodo storico"
              button-style="btn-primary btn-sm"
            >
              <x-slot:body>
                <form
                  class="form"
                  method="POST"
                  id="formPeriodoStorico{{ $persona->id }}"
                  action="{{ route("nomadelfia.join-leave-history.store", $persona->id) }}"
                >
                  @csrf
                  <p>Registra un periodo passato in cui {{ $persona->nominativo }} ha vissuto in Nomadelfia.</p>
                  <div class="mb-3">
                    <label class="form-label" for="storico_data_entrata">
                      Data entrata
                    </label>
                    <input
                      type="date"
                      id="storico_data_entrata"
                      name="data_entrata"
                      value="{{ old("data_entrata") }}"
                      class="form-control"
                      required
                    />
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="storico_data_uscita">
                      Data uscita
                    </label>
                    <input
                      type="date"
                      id="storico_data_uscita"
                      name="data_uscita"
                      value="{{ old("data_uscita") }}"
                      class="form-control"
                      required
                    />
                  </div>
                  <small class="form-text text-muted">
                    Posizioni, gruppi familiari e famiglie della persona non
                    vengono modificati.
                  </small>
                </form>
              </x-slot:body>
              <x-slot:footer>
                <button
                  class="btn btn-success"
                  form="formPeriodoStorico{{ $persona->id }}"
                >
                  Salva
                </button>
              </x-slot:footer>
            </x-modal>
          @endcan
        </div>
        <div class="card-body">
          <div class="card-body">
            <div class="row">
              <p class="col-md-2 fw-bold">Data entrata</p>
              <p class="col-md-2 fw-bold">Data uscita</p>
              <p class="col-md-2 fw-bold">Durata</p>
              <p class="col-md-6 fw-bold">Operazioni</p>
            </div>

            @forelse ($storico as $storicoPopolazione)
              <div class="row">
                <p class="col-md-2">{{ $storicoPopolazione->data_entrata }}</p>
                <p class="col-md-2">{{ $storicoPopolazione->data_uscita }}</p>

                <div class="col-md-2">
                  <span class="badge text-bg-info">
                    {{ Carbon::parse($storicoPopolazione->data_entrata)->diffForHumans(Carbon::parse($storicoPopolazione->data_uscita), ["short" => true]) }}
                  </span>
                </div>
                <div class="col-md-6">
                  @include(
                                        "nomadelfia.templates.modificaDataEntrata",
                                        [
                                            "persona" => $persona,
                                            "data_entrata" => $storicoPopolazione->data_entrata,
                                        ]
                                    )

                  @include(
                                        "nomadelfia.templates.modificaDataUscita",
                                        ["persona" => $persona, "data_uscita" => $storicoPopolazione->data_uscita]
                                    )
                </div>
              </div>
            @empty
              <p class="text-danger">Nessuna posizione nello storico</p>
            @endforelse
          </div>
          <!--end card body-->
        </div>
        <!--end card -->
      </div>
      <!--end card -->
    </div>
    <!--end col -md-6 -->
  </div>
  <!--end row justify-->
@endsection
