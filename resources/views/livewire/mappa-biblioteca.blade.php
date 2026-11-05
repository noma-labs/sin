<div class="mappa-biblioteca">
  <div class="row g-3 mb-2">
    <div class="col-md-6">
      <label for="mappa-cerca" class="form-label"
        >Cerca un argomento o una collocazione</label
      >
      <input
        id="mappa-cerca"
        type="search"
        class="form-control"
        autocomplete="off"
        placeholder="santi, gialli, Maremma, DGB039…"
        wire:model.live.debounce.300ms="cerca"
      />
    </div>
  </div>

  <p class="small text-muted mb-2" aria-live="polite">
    @if ($argomentoElaborato !== null)
      @php
        $consigliato = $argomentiElaborati[$argomentoElaborato];
      @endphp
      <strong class="text-body">{{ $consigliato["nome"] }}</strong>
      : metti il nuovo elaborato in
      @foreach ($consigliato["colonne"] as $codice)
        @if (! $loop->first)
          oppure
        @endif

        <strong class="text-body">{{ $codice }}</strong>
        (prossimo
        <span
          class="font-monospace"
          >{{ $this->prossimaCollocazione($codice) }}</span
        >
        ){{ $loop->last ? "." : "," }}
      @endforeach
      @if ($secondarie !== [])
        Ci sono elaborati di questo argomento anche in {{ implode(", ", $secondarie) }}.
      @endif
    @elseif ($evidenziate !== null && $area === null)
      @if ($evidenziate === [])
        Nessuna colonna per «{{ $cerca }}». Prova con un'altra parola o una collocazione come DGB039.
      @else
        Colonne per «{{ $cerca }}»: {{ implode(", ", $evidenziate) }}.
      @endif
    @else
      Tocca una colonna per vedere argomento e ripiani, oppure scegli un'area
      dalla legenda.
    @endif
  </p>

  <div class="d-flex flex-wrap gap-1 mb-2" role="group" aria-label="Aree">
    @foreach ($aree as $chiave => $nome)
      <button
        type="button"
        wire:key="area-{{ $chiave }}"
        wire:click="selezionaArea('{{ $chiave }}')"
        class="btn btn-sm mappa-legenda {{ $area === $chiave ? "active" : "" }}"
        aria-pressed="{{ $area === $chiave ? "true" : "false" }}"
      >
        <span class="mappa-colore mappa-area-{{ $chiave }}"></span>
        {{ $nome }}
      </button>
    @endforeach
  </div>

  @if ($area === \App\Livewire\MappaBiblioteca::AREA_ELABORATI)
    <div
      class="mappa-sottomenu d-flex flex-wrap gap-1 mb-2"
      role="group"
      aria-label="Argomento del nuovo elaborato"
    >
      <span class="w-100 small text-muted">Argomento del nuovo elaborato</span>
      @foreach ($argomentiElaborati as $chiave => $argomento)
        <button
          type="button"
          wire:key="elaborato-{{ $chiave }}"
          wire:click="selezionaArgomentoElaborato('{{ $chiave }}')"
          class="btn btn-sm mappa-legenda {{ $argomentoElaborato === $chiave ? "active" : "" }}"
          aria-pressed="{{ $argomentoElaborato === $chiave ? "true" : "false" }}"
          title="{{ $argomento["descrizione"] }}"
        >
          {{ $argomento["nome"] }} ({{ $argomento["elaborati"] }})
        </button>
      @endforeach
    </div>
  @endif

  <div class="row g-3">
    <div class="col-lg-9">
      <div class="mappa-contenitore">
        <svg
          viewBox="{{ $viewBox }}"
          role="img"
          aria-label="Piantina della biblioteca con le colonne degli scaffali"
          @class(["mappa-svg", "mappa-filtrata" => $evidenziate !== null])
        >
          <g class="mappa-arredi">
            <path
              class="mappa-muro"
              d="M110 240 H1005 V652 M1005 680 V705 M1037 705 V845 H720 V715 M860 715 V845 M690 715 V850 H622 M576 850 H425 V715 M398 715 V850 H240 V715 H130 V240 H110"
            />
            <rect class="mappa-finestra" x="228" y="228" width="122" height="12" />
            <rect class="mappa-finestra" x="688" y="228" width="134" height="12" />
            <text class="mappa-etichetta" x="262" y="224">FINESTRA</text>
            <text class="mappa-etichetta" x="724" y="224">FINESTRA</text>
            <ellipse class="mappa-arredo" cx="395" cy="420" rx="135" ry="46" />
            <text class="mappa-etichetta" x="370" y="424">TAVOLO</text>
            <ellipse class="mappa-arredo" cx="752" cy="468" rx="88" ry="24" />
            <text class="mappa-etichetta" x="727" y="472">TAVOLO</text>
            <rect class="mappa-arredo" x="700" y="322" width="95" height="54" />
            <text class="mappa-etichetta" x="738" y="353">PC</text>
            <path class="mappa-muro mappa-muro-interno" d="M285 715 V790 H281 M245 790 H240" />
            <text class="mappa-etichetta" x="251" y="738">WC</text>
            <rect class="mappa-arredo mappa-vuoto" x="425" y="592" width="70" height="32" />
            <text class="mappa-etichetta" x="438" y="612">VUOTO</text>
            <g class="mappa-porta-disegno mappa-porta-emergenza" aria-label="Uscita di emergenza">
              <path class="mappa-porta-apertura" d="M1033 680 A28 28 0 0 0 1005 652" />
              <path class="mappa-porta-anta" d="M1005 680 H1033" />
              <circle class="mappa-porta-cardine" cx="1005" cy="680" r="2.5" />
              <text class="mappa-etichetta mappa-etichetta-porta" x="1010" y="636">
                USCITA DI
              </text>
              <text class="mappa-etichetta mappa-etichetta-porta" x="1010" y="647">
                EMERGENZA
              </text>
            </g>
            <g class="mappa-porta-disegno" aria-label="Porta della stanza sotto DC">
              <path class="mappa-porta-apertura" d="M829 743 A40 40 0 0 1 817 715" />
              <path class="mappa-porta-anta" d="M857 715 L829 743" />
              <circle class="mappa-porta-cardine" cx="857" cy="715" r="2.5" />
            </g>
            <g class="mappa-porta-disegno" aria-label="Porta della stanza sotto DB">
              <path class="mappa-porta-apertura" d="M891 743 A40 40 0 0 0 903 715" />
              <path class="mappa-porta-anta" d="M863 715 L891 743" />
              <circle class="mappa-porta-cardine" cx="863" cy="715" r="2.5" />
            </g>
            <g class="mappa-porta-disegno" aria-label="Porta tra DG e DF">
              <path class="mappa-porta-apertura" d="M566 633 A45 45 0 0 1 611 678" />
              <path class="mappa-porta-anta" d="M566 678 V633" />
              <circle class="mappa-porta-cardine" cx="566" cy="678" r="2.5" />
            </g>
            <g class="mappa-porta-disegno" aria-label="Porta tra DM e DL">
              <path class="mappa-porta-apertura" d="M295 750 A35 35 0 0 0 330 715" />
              <path class="mappa-porta-anta" d="M295 715 V750" />
              <circle class="mappa-porta-cardine" cx="295" cy="715" r="2.5" />
            </g>
            <g class="mappa-porta-disegno" aria-label="Porta del WC">
              <path class="mappa-porta-apertura" d="M281 754 A36 36 0 0 0 245 790" />
              <path class="mappa-porta-anta" d="M281 790 V754" />
              <circle class="mappa-porta-cardine" cx="281" cy="790" r="2.5" />
            </g>
            <g class="mappa-porta-disegno" aria-label="Ingresso principale, tra HF e HE">
              <path class="mappa-porta-apertura" d="M622 804 A46 46 0 0 0 576 850" />
              <path class="mappa-porta-anta" d="M622 850 V804" />
              <circle class="mappa-porta-cardine" cx="622" cy="850" r="2.5" />
              <text class="mappa-etichetta mappa-etichetta-porta" x="599" y="866" text-anchor="middle">
                INGRESSO
              </text>
            </g>
          </g>

          @foreach ($forme as $forma)
            @php
              $codice = $forma["codice"];
              $info = $colonne[$codice];
              $stato = match (true) {
                  $evidenziate === null => "normale",
                  in_array($codice, $evidenziate, true) => "evidenziata",
                  in_array($codice, $secondarie, true) => "secondaria",
                  default => "attenuata",
              };
              $verticale = $forma["altezza"] > $forma["larghezza"] * 1.3;
              $conArgomento = ! $verticale && $forma["larghezza"] >= 58;
              $etichetta = str_starts_with($codice, "Z-") ? "Z" : $codice;
            @endphp

            <g
              wire:key="colonna-{{ $codice }}"
              wire:click="selezionaColonna('{{ $codice }}')"
              wire:keydown.enter="selezionaColonna('{{ $codice }}')"
              tabindex="0"
              role="button"
              aria-label="{{ $etichetta }}: {{ $info["argomento"] }}"
              data-colonna="{{ $codice }}"
              data-stato="{{ $stato }}"
              @class([
                  "mappa-colonna",
                  "mappa-area-" . $info["area"],
                  "mappa-" . $stato,
                  "mappa-selezionata" => $colonna === $codice,
              ])
            >
              <title>{{ $etichetta }} · {{ $info["argomento"] }}</title>
              <rect
                x="{{ $forma["x"] }}"
                y="{{ $forma["y"] }}"
                width="{{ $forma["larghezza"] }}"
                height="{{ $forma["altezza"] }}"
                rx="2"
              />
              <text
                class="mappa-codice"
                x="{{ $forma["x"] + $forma["larghezza"] / 2 }}"
                y="{{ $forma["y"] + ($conArgomento ? 13 : $forma["altezza"] / 2 + 4) }}"
                text-anchor="middle"
              >
                {{ $etichetta }}
              </text>
              @if ($conArgomento)
                <text
                  class="mappa-argomento"
                  x="{{ $forma["x"] + $forma["larghezza"] / 2 }}"
                  y="{{ $forma["y"] + 24 }}"
                  text-anchor="middle"
                >
                  {{ \Illuminate\Support\Str::limit($info["argomento"], (int) floor($forma["larghezza"] / 3.3) - 1, "…") }}
                </text>
              @endif
            </g>
          @endforeach
        </svg>
      </div>
      <p class="small text-muted mt-1">In rosso le porte, in verde l'uscita di emergenza. VUOTO: spazio senza libri.</p>
    </div>

    <div class="col-lg-3">
      <div class="card mappa-scheda">
        <div class="card-body">
          @if ($colonna === null)
            <h5 class="card-title">Scegli una colonna</h5>
            <p class="card-text small text-muted">Tocca un rettangolo della piantina o cerca un argomento. La collocazione si legge così: DGB039 è parete D, colonna G, ripiano B, posizione 39.</p>
          @else
            @php
              $info = $colonne[$colonna];
              $dati = $this->statistiche[$colonna] ?? ["libri" => 0, "ultimoNumero" => 0, "ripiani" => []];
              $isVideo = str_starts_with($colonna, "Z-");
              $isElaborati = $info["area"] === \App\Livewire\MappaBiblioteca::AREA_ELABORATI;
            @endphp

            <div class="small text-muted text-uppercase">Colonna</div>
            <div class="d-flex align-items-baseline gap-2 flex-wrap">
              <h4 class="font-monospace mb-0">
                {{ $isVideo ? "Z" : $colonna }}
              </h4>
              @if ($isVideo)
                <span class="text-muted"
                  >{{ array_sum($sigleVideo) }} video</span
                >
              @else
                <span class="text-muted"
                  >{{ $dati["libri"] }} {{ $dati["libri"] === 1 ? "libro" : "libri" }}</span
                >
              @endif
            </div>
            <div class="my-2">
              <span class="badge mappa-badge">
                <span
                  class="mappa-colore mappa-area-{{ $info["area"] }}"
                ></span>
                {{ $aree[$info["area"]] }}
              </span>
            </div>
            <h6>{{ $info["argomento"] }}</h6>
            <p class="small text-muted">{{ $info["dettaglio"] }}</p>

            @if ($dati["ripiani"] !== [])
              @php
                $massimo = max($dati["ripiani"]);
              @endphp
              <div class="small text-muted text-uppercase">Ripiani</div>
              <ul class="list-unstyled small mappa-ripiani">
                @foreach ($dati["ripiani"] as $ripiano => $libri)
                  <li wire:key="ripiano-{{ $ripiano }}">
                    <span class="font-monospace fw-bold">{{ $ripiano }}</span>
                    <span class="mappa-barra">
                      <span
                        style="width: {{ round((100 * $libri) / $massimo) }}%"
                      ></span>
                    </span>
                    <span>{{ $libri }}</span>
                  </li>
                @endforeach
              </ul>
            @endif

            @if ($isElaborati && isset(\App\Biblioteca\ArgomentiBiblioteca::ELABORATI_PER_COLONNA[$colonna]))
              @php
                $composizione = \App\Biblioteca\ArgomentiBiblioteca::ELABORATI_PER_COLONNA[$colonna];
              @endphp
              <div class="small text-muted text-uppercase">
                Argomenti degli elaborati
              </div>
              <ul class="list-unstyled small mb-2">
                @foreach ($composizione as $chiave => $elaborati)
                  <li wire:key="composizione-{{ $chiave }}">
                    {{ $argomentiElaborati[$chiave]["nome"] }}
                    <span class="text-muted">{{ $elaborati }}</span>
                  </li>
                @endforeach
              </ul>
              <p class="small">
                Prossima collocazione libera:
                <strong
                  class="font-monospace"
                  >{{ $this->prossimaCollocazione($colonna) }}</strong
                >
              </p>
            @endif

            @if ($classificazioni !== [] && ! $isElaborati)
              <div class="small text-muted text-uppercase">
                Classificazioni principali
              </div>
              <ul class="list-unstyled small mb-2">
                @foreach ($classificazioni as $descrizione => $libri)
                  <li wire:key="classificazione-{{ $loop->index }}">
                    {{ \Illuminate\Support\Str::lower($descrizione) }}
                    <span class="text-muted">{{ $libri }}</span>
                  </li>
                @endforeach
              </ul>
            @endif

            @if ($isVideo)
              <div class="small text-muted text-uppercase">Sigle</div>
              <ul class="list-unstyled small mb-0">
                @foreach ($sigleVideo as $sigla => $video)
                  <li wire:key="sigla-{{ $sigla }}">
                    <span class="font-monospace fw-bold">{{ $sigla }}</span>
                    {{ \App\Biblioteca\ArgomentiBiblioteca::SIGLE_VIDEO[$sigla] ?? "" }}
                    <span class="text-muted">{{ $video }}</span>
                  </li>
                @endforeach
              </ul>
            @else
              <a
                href="{{ route("books.search", ["xCollocazione" => $colonna]) }}"
                class="btn btn-sm btn-outline-primary"
              >
                Vedi i libri di {{ $colonna }}
              </a>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
