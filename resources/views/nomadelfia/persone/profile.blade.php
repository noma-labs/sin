@extends("nomadelfia.index")

@section("content")
  @php
    $nascita = Carbon::parse($persona->data_nascita);
    $ultimoPeriodo = $periodi->last();
    $anniInNomadelfia = $periodi->sum(fn ($p) => $p["entrata"]->diffInDays($p["uscita"] ?? now())) / 365;
    $puoModificare = auth()->user()?->can("popolazione.persona.modifica") ?? false;
    $biografiaLunga = $persona->biografia && (mb_strlen($persona->biografia) > 180 || str_contains($persona->biografia, "\n"));
  @endphp

  <div class="person-profile">
    <header class="profile-band profile-band-solo rounded-3 mb-4">
      <div class="d-flex flex-wrap gap-3 align-items-start">
        <div class="profile-avatar" aria-hidden="true">
          {{ mb_substr($persona->nome, 0, 1) }}{{ mb_substr($persona->cognome, 0, 1) }}
        </div>
        <div class="flex-grow-1" style="min-width: 0">
          <div class="profile-occhiello">Persona</div>
          <h1 class="profile-name mb-0">
            {{ $persona->nome }} {{ $persona->cognome }}
          </h1>
          <div class="d-flex flex-wrap gap-2 my-2">
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
            @else
              <span class="profile-chip profile-chip-mancante">
                N° elenco mancante
              </span>
            @endif

            @if ($posizioneAttuale)
              <span
                class="profile-chip"
                title="Posizione dal {{ Carbon::parse($posizioneAttuale->pivot->data_inizio)->translatedFormat("j M Y") }}"
              >
                {{ $posizioneAttuale->nome }}
              </span>
            @elseif ($isPresente)
              <span class="profile-chip profile-chip-mancante">
                Posizione mancante
              </span>
            @endif

            <span
              class="profile-chip"
              @if ($statoCivile["dal"])
                title="Stato familiare dal {{ $statoCivile["dal"]->translatedFormat("j M Y") }}"
              @endif
            >
              {{ $statoCivile["nome"] }}
            </span>
            @foreach ($statiAttivi as $stato)
              <span
                class="profile-chip"
                title="Stato familiare dal {{ Carbon::parse($stato->pivot->data_inizio)->translatedFormat("j M Y") }}"
              >
                {{ Illuminate\Support\Str::ucfirst(mb_strtolower($stato->nome)) }}
              </span>
            @endforeach
            @if ($famigliaSenzaConiuge)
              <span
                class="profile-chip"
                @if ($famigliaSenzaConiuge->data_creazione)
                  title="Stato familiare dal {{ Carbon::parse($famigliaSenzaConiuge->data_creazione)->translatedFormat("j M Y") }}"
                @endif
              >
                {{ $famigliaSenzaConiuge->tipo->nome() }}
              </span>
            @endif

            @if ($gruppoAttuale)
              <span
                class="profile-chip"
                @if ($gruppoAttuale->pivot->data_entrata_gruppo)
                  title="Nel gruppo dal {{ Carbon::parse($gruppoAttuale->pivot->data_entrata_gruppo)->translatedFormat("j M Y") }}"
                @endif
              >
                Gruppo {{ $gruppoAttuale->nome }}
              </span>
            @elseif ($isPresente)
              <span class="profile-chip profile-chip-mancante">
                Gruppo mancante
              </span>
            @endif

            @foreach ($aziende as $azienda)
              <a
                class="profile-chip"
                href="{{ route("nomadelfia.aziende.edit", $azienda->id) }}"
                @if ($azienda->pivot->data_inizio_azienda)
                  title="Nell'azienda dal {{ Carbon::parse($azienda->pivot->data_inizio_azienda)->translatedFormat("j M Y") }}"
                @endif
              >
                {{ $azienda->nome_azienda }} &middot; {{ mb_strtolower($azienda->pivot->mansione) }}
              </a>
            @endforeach

            @foreach ($incarichi as $incarico)
              <a
                class="profile-chip"
                href="{{ route("nomadelfia.incarichi.edit", $incarico->id) }}"
                @if ($incarico->pivot->data_inizio)
                  title="Incarico dal {{ Carbon::parse($incarico->pivot->data_inizio)->translatedFormat("j M Y") }}"
                @endif
              >
                {{ $incarico->nome }}
              </a>
            @endforeach
          </div>
          <div class="profile-muted mb-1">
            @if (\App\View\Components\PersonaNome::nominativoDiverso($persona))
              Conosciuto come
              <strong>{{ $persona->nominativo }}</strong>
              &middot;
            @endif
            @if ($persona->cf)
              CF {{ $persona->cf }}
            @else
              <span class="text-danger">Nessun codice fiscale</span>
            @endif
          </div>
          <div class="d-flex flex-wrap column-gap-3 row-gap-1 mb-2">
            <span>
              <span class="profile-label">Sesso</span>
              {{ $persona->isMaschio() ? "Maschio" : "Femmina" }}
            </span>
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
          @if ($persona->biografia || $puoModificare)
            <div class="profile-bio mb-2" data-bio>
              <div data-bio-view>
                @if ($persona->biografia)
                  <p
                    class="profile-bio-text mb-1 @if ($biografiaLunga) profile-bio-clamp @endif"
                    id="biografia-testo"
                  >{{ $persona->biografia }}</p>
                @endif
                <div class="d-flex flex-wrap column-gap-3 small">
                  @if ($biografiaLunga)
                    <button
                      class="btn btn-link btn-sm p-0"
                      type="button"
                      aria-expanded="false"
                      aria-controls="biografia-testo"
                      data-bio-toggle
                    >
                      Leggi tutto
                    </button>
                  @endif
                  @if ($puoModificare)
                    <button
                      class="btn btn-link btn-sm p-0"
                      type="button"
                      data-bio-edit
                    >
                      {{ $persona->biografia ? "Modifica biografia" : "Aggiungi una biografia" }}
                    </button>
                  @endif
                </div>
              </div>
              @if ($puoModificare)
                <form
                  method="POST"
                  action="{{ route("anagrafe.person.biografia.update", $persona->id) }}"
                  data-bio-form
                  hidden
                >
                  @csrf
                  @method("PUT")
                  <label class="visually-hidden" for="biografia"
                    >Biografia</label
                  >
                  <textarea
                    class="form-control mb-2"
                    id="biografia"
                    name="biografia"
                    rows="5"
                    >{{ $persona->biografia }}</textarea
                  >
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success" type="submit">
                      Salva
                    </button>
                    <button
                      class="btn btn-sm btn-outline-secondary"
                      type="button"
                      data-bio-cancel
                    >
                      Annulla
                    </button>
                  </div>
                </form>
              @endif
            </div>
          @endif
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

              @can("scuola.visualizza")
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a
                    class="dropdown-item"
                    href="{{ route("scuola.student.show", $persona->id) }}"
                  >
                    Scheda scuola
                  </a>
                </li>
              @endcan
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
              <li>
                <a
                  class="dropdown-item"
                  href="{{ route("nomadelfia.persone.stato", $persona->id) }}"
                >
                  Stato familiare
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

    <div class="row g-4">
      <section class="col-xl-7 col-md-6" aria-labelledby="titolo-percorso">
        <div
          class="d-flex justify-content-between align-items-center gap-2 mb-3"
        >
          <h2 id="titolo-percorso" class="h5 mb-0">Percorso in Nomadelfia</h2>
          @if (auth()->user()?->can("popolazione.persona.inserisci") || $puoModificare)
            @include("nomadelfia.persone.partials.comunita-aggiungi")
          @endif
        </div>
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
                {{ collect([$isPresente ? "Presente" : null, $posizioneAttuale?->nome, $statoCivile["nome"]])->merge($statiAttivi->map(fn ($stato) => Illuminate\Support\Str::ucfirst(mb_strtolower($stato->nome))))->push($famigliaSenzaConiuge?->tipo->nome())->filter()->implode(" · ") ?: "Nessuna situazione attuale" }}
              </div>
            </div>
          </li>
          @foreach ($timeline as $evento)
            <li
              class="profile-event profile-event-{{ $evento->categoria }} @if ($evento->importante) profile-event-importante @endif"
              data-categoria="{{ $evento->categoria }}"
            >
              <div class="profile-event-when">
                @if ($evento->dataRegistrata)
                  <b>{{ $evento->data->year }}</b>
                  <small>{{ $evento->eta }}</small>
                @else
                  <b>&mdash;</b>
                @endif
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
                  @elseif ($evento->famiglia)
                    <a
                      href="{{ route("anagrafe.families.show", $evento->famiglia->id) }}"
                    >
                      {{ $evento->titolo }}
                    </a>
                  @else
                    {{ $evento->titolo }}
                  @endif
                </div>
                <div class="text-body-secondary small">
                  {{ $evento->dataRegistrata ? $evento->data->translatedFormat("j M Y") : "Data non registrata" }}
                  @if ($evento->dettaglio)
                    &middot; {{ $evento->dettaglio }}
                  @endif
                </div>
                @if ($evento->periodo && $puoModificare)
                  @include("nomadelfia.persone.partials.percorso-periodo-azioni", ["periodo" => $evento->periodo, "uscita" => $evento->confinePeriodo === App\Nomadelfia\Persona\DataTransferObjects\TimelineEvent::PERIODO_USCITA])
                @endif
              </div>
            </li>
          @endforeach
        </ol>
      </section>

      <section
        class="col-xl-5 col-md-6"
        id="parentele"
        aria-labelledby="titolo-famiglia"
      >
        @php
          $padre = $genitori->first(fn ($genitore) => $genitore->isMaschio());
          $madre = $genitori->first(fn ($genitore) => ! $genitore->isMaschio());
          // a famiglia d'origine without coniuge (a mamma di vocazione) has only one genitore
          $origineSenzaConiuge = $famigliaOrigine && in_array($famigliaOrigine->tipo, App\Anagrafe\Famiglia\Models\TipoFamiglia::senzaConiuge(), true);
          $puoAggiungereComeFiglio = ! $nelNucleo && auth()->user()?->can("popolazione.persona.inserisci");
          $modalGenitori = $famigliaOrigine
              ? (auth()->user()?->can("popolazione.persona.modifica") ? "modal-aggiungi-genitore" : null)
              : ($puoAggiungereComeFiglio ? "modal-aggiungi-come-figlio" : null);
          $genitoriMancanti = $origineSenzaConiuge
              ? []
              : array_filter(["padre" => $padre === null, "madre" => $madre === null]);

          $puoModificare = (bool) auth()->user()?->can("popolazione.persona.modifica");
          $famigliaPropria = $unione;
          $famigliaPropriaConConiuge = $famigliaPropria && ! in_array($famigliaPropria->tipo, App\Anagrafe\Famiglia\Models\TipoFamiglia::senzaConiuge(), true);
          $modalConiuge = $famigliaPropriaConConiuge && ! $coniuge && $puoModificare ? "modal-aggiungi-coniuge" : null;
          $puoAggiungereFigli = $famigliaPropria && $puoModificare;
          $puoAggiungereFratelli = $famigliaOrigine && $puoModificare;
          $posizioniFiglio = fn (bool $femmina): array => [
              "FIGLIO NATO" => [$femmina ? "Figlia nata" : "Figlio nato", "Nasce nella famiglia"],
              "FIGLIO ACCOLTO" => [$femmina ? "Figlia accolta" : "Figlio accolto", $femmina ? "Accolta dalla famiglia" : "Accolto dalla famiglia"],
              "AGGREGATO" => [$femmina ? "Aggregata" : "Aggregato", $femmina ? "Vive con la famiglia senza esserne figlia" : "Vive con la famiglia senza esserne figlio"],
          ];
          $etichettaStato = fn (App\Nomadelfia\PopolazioneNomadelfia\Models\Stato $stato): string => match ($stato->stato) {
              "VED" => $persona->isMaschio() ? "Vedovo" : "Vedova",
              "SEP" => $persona->isMaschio() ? "Separato" : "Separata",
              "SAC" => "Sacerdote",
              default => Illuminate\Support\Str::ucfirst(mb_strtolower($stato->nome)),
          };
          $notaFiglio = $famigliaPropria ? null : "serve una famiglia";
          $notaFratello = $famigliaOrigine ? null : "servono i genitori";
          $azioniParentele = [
              [
                  "etichetta" => "Coniuge",
                  "modal" => $modalConiuge,
                  "href" => ! $famigliaPropriaConConiuge && auth()->user()?->can("popolazione.persona.inserisci") ? route("anagrafe.families.create", ["persona" => $persona->id, "tipo" => App\Anagrafe\Famiglia\Models\TipoFamiglia::Coniugi->value]) : null,
                  "nota" => $coniuge ? "già presente" : null,
              ],
              ["etichetta" => "Figlio", "modal" => $puoAggiungereFigli ? "modal-aggiungi-figlio" : null, "href" => null, "nota" => $notaFiglio],
              ["etichetta" => "Figlia", "modal" => $puoAggiungereFigli ? "modal-aggiungi-figlia" : null, "href" => null, "nota" => $notaFiglio],
              ["etichetta" => "Fratello", "modal" => $puoAggiungereFratelli ? "modal-aggiungi-fratello" : null, "href" => null, "nota" => $notaFratello],
              ["etichetta" => "Sorella", "modal" => $puoAggiungereFratelli ? "modal-aggiungi-sorella" : null, "href" => null, "nota" => $notaFratello],
          ];
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 id="titolo-famiglia" class="h5 mb-0">Parentele</h2>
          @if ($puoModificare || auth()->user()?->can("popolazione.persona.inserisci"))
            @include("nomadelfia.persone.partials.parentele-aggiungi", ["stileBottone" => "btn btn-sm profile-btn rounded-pill", "titoloBottone" => "Aggiungi"])
          @endif
        </div>

        @if ($famigliaEnrico)
          <div class="alert alert-warning py-2 small" role="alert">
            <strong>ALFA Enrico:</strong>
            {{ Illuminate\Support\Str::headline($famigliaEnrico->famiglia) }}
          </div>
        @endif

        <div
          class="profile-parentele-intestazione d-flex flex-wrap justify-content-between align-items-baseline gap-2"
        >
          <h3 class="profile-parentele-titolo">Genitori</h3>
          @if ($famigliaOrigine)
            <span class="small">
              @include("nomadelfia.persone.partials.parentele-link-famiglia", ["famiglia" => $famigliaOrigine])
            </span>
          @endif
        </div>
        @if ($origini->count() > 1)
          <nav
            class="profile-origini"
            aria-label="Famiglie d'origine di {{ $persona->nome }}"
          >
            @foreach ($origini as $origine)
              <a
                class="profile-filter"
                href="{{ route("nomadelfia.person.profile", ["id" => $persona->id, "origine" => $origine->id, "unione" => request()->integer("unione") ?: null]) }}#parentele"
                @if ($origine->id === $famigliaOrigine?->id) aria-current="true" @endif
              >
                {{ $origine->nome_famiglia . " · " . \App\Anagrafe\Famiglia\DataTransferObjects\AlberoFamiliare::etichettaTappa($persona, $origine->pivot->posizione_famiglia, $origine->pivot->data_entrata) }}
              </a>
            @endforeach
          </nav>
        @endif

        @foreach (["padre" => $padre, "madre" => $madre] as $ruolo => $genitore)
          @if ($genitore)
            @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $genitore, "ruolo" => Illuminate\Support\Str::ucfirst($ruolo), "indentato" => false])
          @elseif (isset($genitoriMancanti[$ruolo]))
            @if ($modalGenitori)
              <button
                type="button"
                class="profile-person profile-person-aggiungi"
                data-bs-toggle="modal"
                data-bs-target="#{{ $modalGenitori }}"
              >
                <span
                  class="profile-person-avatar profile-person-piu"
                  aria-hidden="true"
                >
                  +
                </span>
                Aggiungi {{ $ruolo }}
              </button>
            @else
              <div
                class="profile-person profile-person-aggiungi profile-person-vuoto"
              >
                <span
                  class="profile-person-avatar profile-person-piu"
                  aria-hidden="true"
                >
                  ?
                </span>
                {{ Illuminate\Support\Str::ucfirst($ruolo) }} non {{ $ruolo === "padre" ? "registrato" : "registrata" }}
              </div>
            @endif
          @endif
        @endforeach

        @if ($unione || $coniuge || $figliNelNucleo->isNotEmpty() || $figliFuoriNucleo->isNotEmpty())
          <div
            class="profile-parentele-intestazione d-flex flex-wrap justify-content-between align-items-baseline gap-2"
          >
            <h3 class="profile-parentele-titolo">Coniuge e figli</h3>
            @if ($unione)
              <span class="small">
                @include("nomadelfia.persone.partials.parentele-link-famiglia", ["famiglia" => $unione])
              </span>
            @endif
          </div>
          @if ($unioni->count() > 1)
            <nav
              class="profile-origini"
              aria-label="Famiglie formate da {{ $persona->nome }}"
            >
              @foreach ($unioni as $candidata)
                <a
                  class="profile-filter"
                  href="{{ route("nomadelfia.person.profile", ["id" => $persona->id, "origine" => request()->integer("origine") ?: null, "unione" => $candidata->id]) }}#parentele"
                  @if ($candidata->id === $unione?->id) aria-current="true" @endif
                >
                  {{ ($candidata->tipo?->nome() ?? "Famiglia") . ($candidata->data_creazione ? ($candidata->tipo === App\Anagrafe\Famiglia\Models\TipoFamiglia::Coniugi ? " · ⚭ " : " · dal ") . Carbon::parse($candidata->data_creazione)->year : "") }}
                </a>
              @endforeach
            </nav>
          @endif
          @if ($coniuge)
            @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $coniuge, "ruolo" => $coniuge->isMaschio() ? "Marito" : "Moglie", "indentato" => false])
          @endif

          @php
            $figli = $figliNelNucleo->concat($figliFuoriNucleo)->sortBy("data_nascita");
          @endphp

          @if ($figli->isNotEmpty())
            <div class="profile-figli">
              @foreach ($figli as $figlio)
                @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $figlio, "ruolo" => Illuminate\Support\Str::ucfirst(mb_strtolower($figlio->pivot->posizione_famiglia)), "indentato" => false, "fuoriNucleo" => $figlio->pivot->stato === "0"])
              @endforeach
            </div>
          @endif

          @if ($puoModificare)
            @include("nomadelfia.persone.partials.parentele-aggiungi", ["stileBottone" => "profile-person profile-person-aggiungi", "titoloBottone" => "Aggiungi parente", "conIcona" => true])
          @endif
        @elseif ($famiglia && $famiglia->id !== $famigliaOrigine?->id)
          <h3 class="profile-parentele-titolo">Famiglia</h3>
          @include("nomadelfia.persone.partials.parentele-famiglia")
        @elseif (! $famiglia)
          <p class="text-body-secondary small mt-3 mb-0">Nessuna famiglia</p>
        @endif

        <a
          class="d-inline-block mt-3 small"
          href="{{ route("anagrafe.person.families", ["id" => $persona->id, "origine" => $famigliaOrigine?->id]) }}"
        >
          Albero familiare
        </a>

        @if ($modalGenitori === "modal-aggiungi-genitore" && $genitoriMancanti !== [])
          @include("nomadelfia.templates.aggiungiComponente", ["famiglia" => $famigliaOrigine, "modalId" => "modal-aggiungi-genitore", "buttonTitle" => "", "selezionata" => isset($genitoriMancanti["padre"]) ? "CAPO FAMIGLIA" : "MOGLIE"])
        @endif

        @if ($modalConiuge)
          @php
            $aggiungeMoglie = $posizioneUnione === "CAPO FAMIGLIA";
          @endphp

          @include("nomadelfia.persone.partials.modal-aggiungi-parente", [
              "modalId" => $modalConiuge,
              "famiglia" => $famigliaPropria,
              "titolo" => $aggiungeMoglie ? "Aggiungi la moglie" : "Aggiungi il marito",
              "descrizione" => ($aggiungeMoglie ? "Diventa la moglie di " : "Diventa il marito di ") . $persona->nome . " nella famiglia " . $famigliaPropria->nome_famiglia . ".",
              "etichettaPersona" => $aggiungeMoglie ? "Moglie" : "Marito",
              "posizione" => $aggiungeMoglie ? "MOGLIE" : "CAPO FAMIGLIA",
              "conferma" => $aggiungeMoglie ? "Aggiungi la moglie" : "Aggiungi il marito",
          ])
        @endif

        @if ($puoAggiungereFigli)
          @foreach (["figlio" => false, "figlia" => true] as $parente => $femmina)
            @include("nomadelfia.persone.partials.modal-aggiungi-parente", [
                "modalId" => "modal-aggiungi-" . $parente,
                "famiglia" => $famigliaPropria,
                "titolo" => $femmina ? "Aggiungi una figlia" : "Aggiungi un figlio",
                "descrizione" => ($femmina ? "Entra come figlia" : "Entra come figlio") . " nella famiglia " . $famigliaPropria->nome_famiglia . ".",
                "etichettaPersona" => Illuminate\Support\Str::ucfirst($parente),
                "posizioni" => $posizioniFiglio($femmina),
                "conferma" => $femmina ? "Aggiungi la figlia" : "Aggiungi il figlio",
            ])
          @endforeach
        @endif

        @if ($puoAggiungereFratelli)
          @foreach (["fratello" => false, "sorella" => true] as $parente => $femmina)
            @include("nomadelfia.persone.partials.modal-aggiungi-parente", [
                "modalId" => "modal-aggiungi-" . $parente,
                "famiglia" => $famigliaOrigine,
                "titolo" => $femmina ? "Aggiungi una sorella" : "Aggiungi un fratello",
                "descrizione" => ($femmina ? "Entra come figlia" : "Entra come figlio") . " nella famiglia d'origine " . $famigliaOrigine->nome_famiglia . ", accanto a " . $persona->nome . ".",
                "etichettaPersona" => Illuminate\Support\Str::ucfirst($parente),
                "posizioni" => $posizioniFiglio($femmina),
                "conferma" => $femmina ? "Aggiungi la sorella" : "Aggiungi il fratello",
            ])
          @endforeach
        @endif

        @if ($puoAggiungereComeFiglio)
          @include("nomadelfia.templates.addPersonToFamiliy", ["persona" => $persona, "modalId" => "modal-aggiungi-come-figlio", "buttonTitle" => ""])
        @endif

        @can("popolazione.persona.inserisci")
          @include("nomadelfia.persone.partials.stato-familiare-modali")
        @endcan
      </section>
    </div>

    @include("nomadelfia.persone.partials.comunita-modali")
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
          <p>Inserire la data di uscita di {{ \App\View\Components\PersonaNome::testo($persona) }}</p>
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
          <p>Inserire la data di decesso di {{ \App\View\Components\PersonaNome::testo($persona) }}</p>
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
    document.querySelectorAll("[data-bio]").forEach(function (bio) {
      const vista = bio.querySelector("[data-bio-view]");
      const form = bio.querySelector("[data-bio-form]");
      const testo = bio.querySelector(".profile-bio-text");
      const espandi = bio.querySelector("[data-bio-toggle]");

      espandi?.addEventListener("click", function () {
        const aperto = testo.classList.toggle("profile-bio-clamp") === false;
        espandi.setAttribute("aria-expanded", aperto ? "true" : "false");
        espandi.textContent = aperto ? "Mostra meno" : "Leggi tutto";
      });
      bio
        .querySelector("[data-bio-edit]")
        ?.addEventListener("click", function () {
          vista.hidden = true;
          form.hidden = false;
          form.querySelector("textarea").focus();
        });
      bio
        .querySelector("[data-bio-cancel]")
        ?.addEventListener("click", function () {
          form.reset();
          form.hidden = true;
          vista.hidden = false;
        });
    });

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
