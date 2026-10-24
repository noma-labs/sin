@extends("nomadelfia.index")

@section("content")
  @php
    $nascita = Carbon::parse($persona->data_nascita);
    $ultimoPeriodo = $periodi->last();
    $anniInNomadelfia = $periodi->sum(fn ($p) => $p["entrata"]->diffInDays($p["uscita"] ?? now())) / 365;
  @endphp

  <div class="person-profile">
    <header class="profile-band rounded-3 mb-0">
      <div class="d-flex flex-wrap gap-3 align-items-start">
        <div class="profile-avatar" aria-hidden="true">
          {{ mb_substr($persona->nome, 0, 1) }}{{ mb_substr($persona->cognome, 0, 1) }}
        </div>
        <div class="flex-grow-1" style="min-width: 0">
          <h1 class="profile-name mb-0">{{ $persona->nominativo }}</h1>
          <div class="profile-muted mb-1">
            {{ $persona->cognome }} {{ $persona->nome }}
            @if ($persona->cf)
              &middot; CF {{ $persona->cf }}
            @endif
          </div>
          <div class="d-flex flex-wrap column-gap-3 row-gap-1 mb-2">
            <span>
              <span class="profile-label">Nascita</span>
              {{ $nascita->translatedFormat("j M Y") }} &middot; {{ $persona->provincia_nascita }}
            </span>
            @if ($persona->isDeceduta())
              <span>
                <span class="profile-label">Decesso</span>
                {{ Carbon::parse($persona->data_decesso)->translatedFormat("j M Y") }}
              </span>
            @else
              <span>
                <span class="profile-label">Età</span>
                {{ $nascita->age }} anni
              </span>
            @endif

            @if ($persona->origine)
              <span>
                <span class="profile-label">Origine</span>
                {{ $persona->origine }}
              </span>
            @endif
          </div>
          <div class="d-flex flex-wrap gap-2">
            @if ($isPresente)
              <span class="profile-chip profile-chip-presente">
                Presente &middot; dal {{ $ultimoPeriodo["entrata"]->translatedFormat("j M Y") }}
              </span>
            @elseif ($persona->isDeceduta())
              <span class="profile-chip">Deceduto</span>
            @elseif ($ultimoPeriodo)
              <span class="profile-chip">
                Uscito &middot; il {{ $ultimoPeriodo["uscita"]->translatedFormat("j M Y") }}
              </span>
            @else
              <span class="profile-chip">Mai entrato</span>
            @endif
            @if ($persona->numero_elenco)
              <span class="profile-chip profile-chip-numero">
                N° elenco {{ $persona->numero_elenco }}
              </span>
            @endif

            @if ($posizioneAttuale)
              <span class="profile-chip">{{ $posizioneAttuale->nome }}</span>
            @endif

            <span class="profile-chip">{{ $statoCivile["nome"] }}</span>
            @foreach ($statiAttivi as $stato)
              <span class="profile-chip">
                {{ Illuminate\Support\Str::ucfirst(mb_strtolower($stato->nome)) }}
              </span>
            @endforeach
            @if ($famigliaDaMamma)
              <span class="profile-chip">
                {{ $famigliaDaMamma->tipo->nome() }}
              </span>
            @endif

            @if ($gruppoAttuale)
              <span class="profile-chip">
                Gruppo {{ $gruppoAttuale->nome }}
              </span>
            @endif
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <div class="dropdown">
            <button
              class="btn profile-btn dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              Popolazione
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              @if ($isPresente)
                <li>
                  <button
                    class="dropdown-item"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#modal-uscita"
                  >
                    Uscita dalla comunità
                  </button>
                </li>
              @else
                <li>
                  <button
                    class="dropdown-item"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#modal-entrata"
                  >
                    Entrata nella comunità
                  </button>
                </li>
              @endif
              @if (! $persona->isDeceduta())
                <li>
                  <button
                    class="dropdown-item text-danger"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#modal-decesso"
                  >
                    Decesso
                  </button>
                </li>
              @endif

              <li><hr class="dropdown-divider" /></li>
              <li>
                <a
                  class="dropdown-item"
                  href="{{ route("nomadelfia.join-leave-history.index", $persona->id) }}"
                >
                  Storico entrate e uscite
                </a>
              </li>
            </ul>
          </div>
          <div class="dropdown">
            <button
              class="btn profile-btn dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              Modifica
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a
                  class="dropdown-item"
                  href="{{ route("anagrafe.person.identity.edit", $persona->id) }}"
                >
                  Dati anagrafici
                </a>
              </li>
              <li>
                <a
                  class="dropdown-item"
                  href="{{ route("anagrafe.internal-name.edit", $persona->id) }}"
                >
                  Nominativo
                </a>
              </li>
              @if (! $persona->numero_elenco)
                <li>
                  <a
                    class="dropdown-item"
                    href="{{ route("nomadelfia.folder-number.create", $persona->id) }}"
                  >
                    Assegna numero elenco
                  </a>
                </li>
              @endif

              <li><hr class="dropdown-divider" /></li>
              <li>
                <a
                  class="dropdown-item"
                  href="{{ route("nomadelfia.person.position.index", $persona->id) }}"
                >
                  Posizione
                </a>
              </li>
              <li>
                <a
                  class="dropdown-item"
                  href="{{ route("nomadelfia.persone.stato", $persona->id) }}"
                >
                  Stato familiare
                </a>
              </li>
              <li>
                <a
                  class="dropdown-item"
                  href="{{ route("nomadelfia.person.gruppo", $persona->id) }}"
                >
                  Gruppo familiare
                </a>
              </li>
              <li>
                <a
                  class="dropdown-item"
                  href="{{ route("nomadelfia.persone.aziende", ["idPersona" => $persona->id]) }}"
                >
                  Aziende
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      @if ($periodi->isNotEmpty())
        <div class="mt-2">
          <div class="d-flex justify-content-between profile-muted small mb-1">
            <span>{{ $nascita->year }}</span>
            <span>
              In Nomadelfia: {{ (int) $anniInNomadelfia }} anni
              @if ($periodi->count() > 1)
                in {{ $periodi->count() }} periodi
              @endif
            </span>
            <span>
              {{ $persona->isDeceduta() ? Carbon::parse($persona->data_decesso)->year : "oggi" }}
            </span>
          </div>
          <div
            class="profile-strip"
            role="img"
            aria-label="Periodi in Nomadelfia"
          >
            @foreach ($periodi as $periodo)
              <span
                style="left: {{ $periodo["left"] }}%; width: {{ $periodo["width"] }}%"
                title="{{ $periodo["entrata"]->toDateString() }} – {{ $periodo["uscita"]?->toDateString() ?? "oggi" }}"
              ></span>
            @endforeach
          </div>
        </div>
      @endif
    </header>

    <ul class="nav nav-underline profile-tabs mb-4 px-3">
      <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="#">Panoramica</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          href="{{ route("nomadelfia.join-leave-history.index", $persona->id) }}"
        >
          Storico comunità
        </a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          href="{{ route("anagrafe.person.families", $persona->id) }}"
        >
          Famiglie
        </a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          href="{{ route("nomadelfia.persone.aziende", ["idPersona" => $persona->id]) }}"
        >
          Aziende
        </a>
      </li>
      @can("scuola.visualizza")
        <li class="nav-item">
          <a
            class="nav-link"
            href="{{ route("scuola.student.show", $persona->id) }}"
          >
            Scuola
          </a>
        </li>
      @endcan

      <li class="nav-item ms-auto">
        <a
          class="nav-link"
          href="{{ route("nomadelfia.person.show", $persona->id) }}"
        >
          Torna alla vista classica
        </a>
      </li>
    </ul>

    <div class="row g-4">
      <div class="col-xl-4 col-md-6 d-flex flex-column gap-4">
        <section aria-labelledby="titolo-anagrafica">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 id="titolo-anagrafica" class="h5 mb-0">Dati anagrafici</h2>
            <a
              class="btn btn-sm btn-outline-secondary"
              href="{{ route("anagrafe.person.identity.edit", $persona->id) }}"
            >
              Modifica
            </a>
          </div>
          <dl class="profile-kv card mb-0">
            <div class="profile-kv-row">
              <dt>Nome</dt>
              <dd>{{ $persona->nome }}</dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Cognome</dt>
              <dd>{{ $persona->cognome }}</dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Nominativo</dt>
              <dd>{{ $persona->nominativo }}</dd>
              <a
                href="{{ route("anagrafe.internal-name.edit", $persona->id) }}"
              >
                Modifica
              </a>
            </div>
            <div class="profile-kv-row">
              <dt>Sesso</dt>
              <dd>{{ $persona->sesso }}</dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Data di nascita</dt>
              <dd>{{ $nascita->format("d/m/Y") }}</dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Luogo di nascita</dt>
              <dd>{{ $persona->provincia_nascita }}</dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Codice fiscale</dt>
              <dd>
                @if ($persona->cf)
                  {{ $persona->cf }}
                @else
                  <span class="text-danger">Nessun codice fiscale</span>
                @endif
              </dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Biografia</dt>
              <dd>
                @if ($persona->biografia)
                  <span
                    style="white-space: pre-line"
                    >{{ $persona->biografia }}</span
                  >
                @else
                  <span class="text-danger">Nessuna biografia</span>
                @endif
              </dd>
              <span></span>
            </div>
          </dl>
        </section>

        <section aria-labelledby="titolo-situazione">
          <h2 id="titolo-situazione" class="h5 mb-3">Situazione attuale</h2>
          <dl class="profile-kv card mb-0">
            <div class="profile-kv-row">
              <dt>Posizione</dt>
              <dd>
                @if ($posizioneAttuale)
                  {{ $posizioneAttuale->nome }}
                  <small>
                    dal {{ Carbon::parse($posizioneAttuale->pivot->data_inizio)->translatedFormat("j M Y") }}
                  </small>
                @else
                  <span class="text-danger">Nessuna posizione</span>
                @endif
              </dd>
              <a
                href="{{ route("nomadelfia.person.position.index", $persona->id) }}"
              >
                Modifica
              </a>
            </div>
            <div class="profile-kv-row">
              <dt>Stato familiare</dt>
              <dd>
                {{ $statoCivile["nome"] }}
                @if ($statoCivile["dal"])
                  <small>
                    dal {{ $statoCivile["dal"]->translatedFormat("j M Y") }}
                  </small>
                @endif
                @foreach ($statiAttivi as $stato)
                  <br />
                  {{ Illuminate\Support\Str::ucfirst(mb_strtolower($stato->nome)) }}
                  <small>
                    dal {{ Carbon::parse($stato->pivot->data_inizio)->translatedFormat("j M Y") }}
                  </small>
                @endforeach
                @if ($famigliaDaMamma)
                  <br />
                  {{ $famigliaDaMamma->tipo->nome() }}
                  @if ($famigliaDaMamma->data_creazione)
                    <small>
                      dal {{ Carbon::parse($famigliaDaMamma->data_creazione)->translatedFormat("j M Y") }}
                    </small>
                  @endif
                @endif
              </dd>
              <a href="{{ route("nomadelfia.persone.stato", $persona->id) }}">
                Modifica
              </a>
            </div>
            <div class="profile-kv-row">
              <dt>Gruppo familiare</dt>
              <dd>
                @if ($gruppoAttuale)
                  <a
                    href="{{ route("nomadelfia.gruppifamiliari.show", $gruppoAttuale->id) }}"
                  >
                    {{ $gruppoAttuale->nome }}
                  </a>
                  @if ($gruppoAttuale->pivot->data_entrata_gruppo)
                    <small>
                      dal {{ Carbon::parse($gruppoAttuale->pivot->data_entrata_gruppo)->translatedFormat("j M Y") }}
                    </small>
                  @endif
                @else
                  <span class="text-danger">Nessun gruppo</span>
                @endif
              </dd>
              <a href="{{ route("nomadelfia.person.gruppo", $persona->id) }}">
                Modifica
              </a>
            </div>
            <div class="profile-kv-row">
              <dt>Aziende</dt>
              <dd>
                @forelse ($aziende as $azienda)
                  <div>
                    <a
                      href="{{ route("nomadelfia.aziende.edit", $azienda->id) }}"
                    >
                      {{ $azienda->nome_azienda }}
                    </a>
                    &middot; {{ mb_strtolower($azienda->pivot->mansione) }}
                    @if ($azienda->pivot->data_inizio_azienda)
                      <small>
                        dal {{ Carbon::parse($azienda->pivot->data_inizio_azienda)->translatedFormat("j M Y") }}
                      </small>
                    @endif
                  </div>
                @empty
                  <span class="text-danger">Nessuna azienda</span>
                @endforelse
              </dd>
              <a
                href="{{ route("nomadelfia.persone.aziende", ["idPersona" => $persona->id]) }}"
              >
                Modifica
              </a>
            </div>
            <div class="profile-kv-row">
              <dt>Incarichi</dt>
              <dd>
                @forelse ($incarichi as $incarico)
                  <div>
                    <a
                      href="{{ route("nomadelfia.incarichi.edit", $incarico->id) }}"
                    >
                      {{ $incarico->nome }}
                    </a>
                    @if ($incarico->pivot->data_inizio)
                      <small>
                        dal {{ Carbon::parse($incarico->pivot->data_inizio)->translatedFormat("j M Y") }}
                      </small>
                    @endif
                  </div>
                @empty
                  <span class="text-body-secondary">Nessun incarico</span>
                @endforelse
              </dd>
              <span></span>
            </div>
            <div class="profile-kv-row">
              <dt>Numero elenco</dt>
              <dd>
                @if ($persona->numero_elenco)
                  {{ $persona->numero_elenco }}
                @else
                  <span class="text-danger">Nessun numero elenco</span>
                @endif
              </dd>
              @if (! $persona->numero_elenco)
                <a
                  href="{{ route("nomadelfia.folder-number.create", $persona->id) }}"
                >
                  Assegna
                </a>
              @else
                <span></span>
              @endif
            </div>
          </dl>
        </section>
      </div>

      <section class="col-xl-5 col-md-6" aria-labelledby="titolo-percorso">
        <h2 id="titolo-percorso" class="h5 mb-3">Percorso in Nomadelfia</h2>
        <div
          class="d-flex flex-wrap gap-2 mb-3"
          role="group"
          aria-label="Filtra eventi"
        >
          <button
            class="profile-filter"
            type="button"
            aria-pressed="true"
            data-categoria="comunita"
          >
            <i class="profile-dot-comunita"></i>
            Comunità
          </button>
          <button
            class="profile-filter"
            type="button"
            aria-pressed="true"
            data-categoria="famiglia"
          >
            <i class="profile-dot-famiglia"></i>
            Famiglia
          </button>
        </div>
        <ol class="profile-timeline">
          <li class="profile-event profile-event-oggi">
            <div class="profile-event-when">
              <b>{{ $persona->isDeceduta() ? "" : "Oggi" }}</b>
              <small>{{ $persona->isDeceduta() ? "" : $nascita->age }}</small>
            </div>
            <div class="profile-event-rail"></div>
            <div class="profile-event-card">
              <div class="fw-semibold">
                {{ collect([$isPresente ? "Presente" : null, $posizioneAttuale?->nome, $statoCivile["nome"]])->merge($statiAttivi->map(fn ($stato) => Illuminate\Support\Str::ucfirst(mb_strtolower($stato->nome))))->push($famigliaDaMamma?->tipo->nome())->filter()->implode(" · ") ?: "Nessuna situazione attuale" }}
              </div>
            </div>
          </li>
          @foreach ($timeline as $evento)
            <li
              class="profile-event profile-event-{{ $evento->categoria }} @if ($evento->importante) profile-event-importante @endif"
              data-categoria="{{ $evento->categoria }}"
            >
              <div class="profile-event-when">
                <b>{{ $evento->data->year }}</b>
                <small>{{ $evento->eta }}</small>
              </div>
              <div class="profile-event-rail"></div>
              <div class="profile-event-card">
                <div class="profile-event-label">{{ $evento->etichetta }}</div>
                <div class="fw-semibold">
                  @if ($evento->persona)
                    <a
                      href="{{ route("nomadelfia.person.profile", $evento->persona->id) }}"
                    >
                      {{ $evento->titolo }}
                    </a>
                  @else
                    {{ $evento->titolo }}
                  @endif
                </div>
                <div class="text-body-secondary small">
                  {{ $evento->data->translatedFormat("j M Y") }}
                  @if ($evento->dettaglio)
                    &middot; {{ $evento->dettaglio }}
                  @endif
                </div>
              </div>
            </li>
          @endforeach
        </ol>
      </section>

      <section class="col-xl-3 col-md-6" aria-labelledby="titolo-famiglia">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 id="titolo-famiglia" class="h5 mb-0">Famiglia</h2>
          <a
            class="btn btn-sm btn-outline-secondary"
            href="{{ route("anagrafe.person.families", $persona->id) }}"
          >
            Storico famiglie
          </a>
        </div>

        @if ($famigliaEnrico)
          <div class="alert alert-warning py-2 small" role="alert">
            <strong>ALFA Enrico:</strong>
            {{ Illuminate\Support\Str::headline($famigliaEnrico->famiglia) }}
          </div>
        @endif

        @if ($famigliaAttuale)
          <div class="fw-semibold">
            <a
              href="{{ route("anagrafe.families.show", $famigliaAttuale->id) }}"
            >
              Famiglia {{ $famigliaAttuale->nome_famiglia }}
            </a>
          </div>
          <div class="text-body-secondary small mb-3">
            {{ Illuminate\Support\Str::ucfirst(mb_strtolower($posizioneFamiglia)) }}
          </div>
        @else
          <p class="text-body-secondary">Nessuna famiglia</p>
        @endif

        @if ($coniuge || $figliNelNucleo->isNotEmpty())
          <div class="text-body-secondary small mb-2">Coniuge e figli</div>
          @if ($coniuge)
            @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $coniuge, "ruolo" => $coniuge->isMaschio() ? "Marito" : "Moglie", "indentato" => false])
          @endif

          @foreach ($figliNelNucleo as $figlio)
            @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $figlio, "ruolo" => Illuminate\Support\Str::ucfirst(mb_strtolower($figlio->pivot->posizione_famiglia)), "indentato" => true])
          @endforeach
        @endif

        @if ($figliFuoriNucleo->isNotEmpty())
          <div class="text-body-secondary small mt-3 mb-2">
            Figli non più nel nucleo
          </div>
          @foreach ($figliFuoriNucleo as $figlio)
            @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $figlio, "ruolo" => Illuminate\Support\Str::ucfirst(mb_strtolower($figlio->pivot->posizione_famiglia)), "indentato" => true, "fuoriNucleo" => true])
          @endforeach
        @endif

        @if ($genitori->isNotEmpty())
          <div class="text-body-secondary small mt-3 mb-2">
            Famiglia d'origine
            <a
              href="{{ route("anagrafe.families.show", $famigliaOrigine->id) }}"
            >
              {{ $famigliaOrigine->nome_famiglia }}
            </a>
          </div>
          @foreach ($genitori as $genitore)
            @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $genitore, "ruolo" => $genitore->isMaschio() ? "Padre" : "Madre", "indentato" => false])
          @endforeach
        @endif

        @can("popolazione.persona.inserisci")
          <div class="d-flex flex-wrap gap-2 mt-3">
            @if ($persona->puoSposarsi())
              <a
                class="btn btn-sm btn-success"
                href="{{ route("anagrafe.marriage.create", ["persona" => $persona->id]) }}"
              >
                Nuovo matrimonio
              </a>
            @endif

            @if (! $famigliaAttuale)
              @include("nomadelfia.templates.addPersonToFamiliy", ["persona" => $persona])
            @endif
          </div>
        @endcan
      </section>
    </div>
  </div>

  @if ($isPresente)
    <x-modal modal-id="modal-uscita" modal-title="Uscita dalla comunità">
      <x-slot:body>
        <form
          class="form"
          method="POST"
          id="formUscitaPersona{{ $persona->id }}"
          action="{{ route("nomadelfia.leave.store", $persona->id) }}"
        >
          @csrf
          <p>Inserire la data di uscita di {{ $persona->nominativo }}</p>
          <input class="form-control" type="date" name="data_uscita" />
        </form>
      </x-slot:body>
      <x-slot:footer>
        <button
          class="btn btn-success"
          form="formUscitaPersona{{ $persona->id }}"
        >
          Salva
        </button>
      </x-slot:footer>
    </x-modal>
  @else
    <x-modal modal-id="modal-entrata" modal-title="Entrata nella comunità">
      <x-slot:body>
        @include("nomadelfia.templates.entrataPersona", ["persona" => $persona])
      </x-slot:body>
    </x-modal>
  @endif

  @if (! $persona->isDeceduta())
    <x-modal modal-id="modal-decesso" modal-title="Decesso di persona">
      <x-slot:body>
        <form
          class="form"
          method="POST"
          id="formDecessoPersona{{ $persona->id }}"
          action="{{ route("nomadelfia.death.store", $persona->id) }}"
        >
          @csrf
          <p>Inserire la data di decesso di {{ $persona->nominativo }}</p>
          <input class="form-control" type="date" name="data_decesso" />
        </form>
      </x-slot:body>
      <x-slot:footer>
        <button
          class="btn btn-danger"
          form="formDecessoPersona{{ $persona->id }}"
        >
          Salva
        </button>
      </x-slot:footer>
    </x-modal>
  @endif

  <script>
    document.querySelectorAll(".profile-filter").forEach(function (filtro) {
      filtro.addEventListener("click", function () {
        const attivo = filtro.getAttribute("aria-pressed") !== "true";
        filtro.setAttribute("aria-pressed", attivo ? "true" : "false");
        document
          .querySelectorAll(
            '.profile-event[data-categoria="' + filtro.dataset.categoria + '"]',
          )
          .forEach(function (evento) {
            evento.hidden = !attivo;
          });
      });
    });
  </script>
@endsection
