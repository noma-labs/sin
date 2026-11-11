@extends("nomadelfia.persone.index")

@section("content")
  @php
    $nascita = $persona->data_nascita ? Carbon::parse($persona->data_nascita) : null;
    $decesso = $persona->data_decesso ? Carbon::parse($persona->data_decesso) : null;
    $attuale = $periodi->firstWhere("data_uscita", null);
    $durata = fn ($da, $a) => $da->diffForHumans($a, \Carbon\CarbonInterface::DIFF_ABSOLUTE, false, 2);
  @endphp

  <div class="person-profile">
    <header class="profile-band profile-band-solo rounded-3 mb-3">
      <div class="d-flex flex-wrap gap-3 align-items-center">
        <div class="flex-grow-1" style="min-width: 0">
          <h1 class="profile-name mb-0">Entrate e uscite</h1>
          <div class="profile-muted">
            <a href="{{ route("nomadelfia.person.profile", $persona->id) }}">
              {{ \App\View\Components\PersonaNome::testo($persona) }}
            </a>
            &middot; {{ $periodi->count() }} {{ $periodi->count() === 1 ? "periodo" : "periodi" }} in
            Nomadelfia
          </div>
        </div>
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
                    @unless ($persona->isDeceduta())
                      <span class="text-body-secondary">(facoltativa)</span>
                    @endunless
                  </label>
                  <input
                    type="date"
                    id="storico_data_uscita"
                    name="data_uscita"
                    value="{{ old("data_uscita") }}"
                    class="form-control"
                    aria-describedby="storico_data_uscita_nota"
                    @required($persona->isDeceduta())
                  />
                  @unless ($persona->isDeceduta())
                    <div
                      id="storico_data_uscita_nota"
                      class="form-text text-warning-emphasis"
                    >
                      Se la data di uscita non è nota, lasciala vuota. Una
                      persona senza data di uscita è considerata nella
                      popolazione attuale: è possibile solo se il periodo
                      inserito è successivo all'ultima data di uscita.
                    </div>
                  @endunless
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
    </header>

    <div class="row justify-content-center">
      <section class="col-xl-6 col-lg-8" aria-label="Entrate e uscite">
        <ol class="profile-timeline">
          @if ($attuale)
            <li class="profile-event profile-event-oggi">
              <div class="profile-event-when">
                <b>Oggi</b>
                <small>{{ $nascita?->age }}</small>
              </div>
              <div class="profile-event-rail"></div>
              <div class="profile-event-card">
                <div class="fw-semibold">Presente in Nomadelfia</div>
                <div class="text-body-secondary small">
                  Da {{ $durata(Carbon::parse($attuale->data_entrata), Carbon::now()) }}
                </div>
              </div>
            </li>
          @endif

          @if ($decesso)
            <li class="profile-event">
              <div class="profile-event-when">
                <b>{{ $decesso->year }}</b>
                <small
                  >{{ $nascita ? (int) $nascita->diffInYears($decesso) : "" }}</small
                >
              </div>
              <div class="profile-event-rail"></div>
              <div class="profile-event-card">
                <div class="profile-event-label">Anagrafe</div>
                <div class="fw-semibold">Decesso</div>
                <div class="text-body-secondary small">
                  {{ $decesso->translatedFormat("j M Y") }}
                </div>
              </div>
            </li>
          @endif

          @foreach ($periodi as $periodo)
            @php
              $entrata = Carbon::parse($periodo->data_entrata);
              $uscita = $periodo->data_uscita ? Carbon::parse($periodo->data_uscita) : null;
              $periodoPrecedente = $periodi->get($loop->index + 1);
            @endphp

            @if ($uscita)
              <li
                class="profile-event profile-event-comunita profile-event-importante"
              >
                <div class="profile-event-when">
                  <b>{{ $uscita->year }}</b>
                  <small
                    >{{ $nascita ? (int) $nascita->diffInYears($uscita) : "" }}</small
                  >
                </div>
                <div class="profile-event-rail"></div>
                <div class="profile-event-card">
                  <div class="profile-event-label">Uscita</div>
                  <div class="fw-semibold">Esce da Nomadelfia</div>
                  <div class="text-body-secondary small">
                    {{ $uscita->translatedFormat("j M Y") }} &middot; Dopo {{ $durata($entrata, $uscita) }} in
                    Nomadelfia
                    @if ($decesso?->isSameDay($uscita))
                      &middot; Per decesso
                    @endif
                  </div>
                  @can("popolazione.persona.modifica")
                    <div class="d-flex flex-wrap gap-2 mt-2">
                      @include("nomadelfia.templates.modificaDataUscita", ["persona" => $persona, "data_uscita" => $periodo->data_uscita, "collegate" => $collegate[$periodo->data_entrata]["uscita"]])
                      @if ($loop->first && $riaperte !== null)
                        @include("nomadelfia.templates.eliminaDataUscita", ["persona" => $persona, "periodo" => $periodo, "riaperte" => $riaperte])
                      @endif
                      @include("nomadelfia.templates.eliminaPeriodoPopolazione", ["persona" => $persona, "periodo" => $periodo])
                    </div>
                  @endcan
                </div>
              </li>
            @endif

            <li
              class="profile-event profile-event-comunita profile-event-importante"
            >
              <div class="profile-event-when">
                <b>{{ $entrata->year }}</b>
                <small
                  >{{ $nascita ? (int) $nascita->diffInYears($entrata) : "" }}</small
                >
              </div>
              <div class="profile-event-rail"></div>
              <div class="profile-event-card">
                <div class="profile-event-label">Entrata</div>
                <div class="fw-semibold">
                  {{ $periodoPrecedente ? "Rientra" : "Entra" }} in Nomadelfia
                </div>
                <div class="text-body-secondary small">
                  {{ $entrata->translatedFormat("j M Y") }}
                  @if ($periodoPrecedente)
                    &middot; Dopo
                    {{ $durata(Carbon::parse($periodoPrecedente->data_uscita), $entrata) }}
                    fuori dalla comunità
                  @endif
                </div>
                @can("popolazione.persona.modifica")
                  <div class="d-flex flex-wrap gap-2 mt-2">
                    @include("nomadelfia.templates.modificaDataEntrata", ["persona" => $persona, "data_entrata" => $periodo->data_entrata, "collegate" => $collegate[$periodo->data_entrata]["entrata"]])
                  </div>
                @endcan
              </div>
            </li>
          @endforeach

          @if ($periodi->isEmpty())
            <li class="profile-event">
              <div class="profile-event-when"></div>
              <div class="profile-event-rail"></div>
              <div class="profile-event-card text-body-secondary">
                Nessuna entrata in Nomadelfia registrata
              </div>
            </li>
          @endif

          @if ($nascita)
            <li class="profile-event">
              <div class="profile-event-when">
                <b>{{ $nascita->year }}</b>
                <small>0</small>
              </div>
              <div class="profile-event-rail"></div>
              <div class="profile-event-card">
                <div class="profile-event-label">Anagrafe</div>
                <div class="fw-semibold">Nascita</div>
                <div class="text-body-secondary small">
                  {{ $nascita->translatedFormat("j M Y") }}
                </div>
              </div>
            </li>
          @endif
        </ol>
      </section>
    </div>
  </div>
@endsection
