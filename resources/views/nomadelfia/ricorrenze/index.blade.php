@extends("nomadelfia.index")

@section("content")
  @use("App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\TipoRicorrenza")

  <div class="person-profile">
    <header class="profile-band profile-band-solo rounded-3 mb-3">
      <div class="d-flex flex-wrap gap-3 align-items-center">
        <div class="flex-grow-1" style="min-width: 0">
          <h1 class="profile-name mb-0">Ricorrenze</h1>
          <div class="profile-muted">
            Oggi, {{ $oggi->translatedFormat("l j F Y") }} &middot; {{ $totale }} {{ $totale === 1 ? "ricorrenza" : "ricorrenze" }} {{ $settimana ? "nei prossimi 7 giorni" : "nei prossimi 30 giorni" }}
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2 d-print-none">
          <div class="btn-group" role="group" aria-label="Periodo">
            <a
              class="btn profile-btn @if ($settimana) active @endif"
              href="{{ route("nomadelfia.ricorrenze", ["periodo" => "settimana"]) }}"
              @if ($settimana) aria-current="true" @endif
            >
              Prossimi 7 giorni
            </a>
            <a
              class="btn profile-btn @if (! $settimana) active @endif"
              href="{{ route("nomadelfia.ricorrenze") }}"
              @if (! $settimana) aria-current="true" @endif
            >
              Prossimi 30 giorni
            </a>
          </div>
          <button
            class="btn profile-btn"
            type="button"
            onclick="window.print()"
          >
            Stampa
          </button>
        </div>
      </div>
    </header>

    <div
      class="profile-filtri-ricorrenze d-flex flex-wrap gap-2 mb-4 d-print-none"
      role="group"
      aria-label="Mostra solo le ricorrenze di un tipo"
    >
      @foreach ($tipi as $tipo)
        <button
          class="profile-filter profile-ricorrenza-{{ $tipo->value }}"
          type="button"
          aria-pressed="false"
          data-tipo="{{ $tipo->value }}"
        >
          <i class="profile-dot-ricorrenza"></i>
          {{ $tipo->nome() }}
          <b>{{ $conteggi->get($tipo->value, 0) }}</b>
        </button>
      @endforeach
    </div>

    <div class="row g-4">
      <section class="col-lg-8" aria-label="Ricorrenze per giorno">
        @if ($giorni->isEmpty())
          <p class="text-body-secondary">Nessuna ricorrenza nel periodo.</p>
        @endif

        <p class="text-body-secondary" id="ricorrenze-nascoste" hidden>Nessuna ricorrenza nel periodo con i filtri scelti.</p>

        <ol class="profile-timeline">
          @foreach ($giorni as $ricorrenzeDelGiorno)
            @php
              $giorno = $ricorrenzeDelGiorno->first()->data;
            @endphp

            <li
              class="profile-event profile-giorno @if ($giorno->isToday()) profile-giorno-oggi @endif"
            >
              <div class="profile-event-when">
                <b>{{ $giorno->day }}</b>
                <small>
                  {{ $giorno->translatedFormat("M") }} &middot; {{ $giorno->translatedFormat("D") }}
                </small>
                @if ($giorno->isToday())
                  <span class="profile-oggi">Oggi</span>
                @endif
              </div>
              <div class="profile-event-rail"></div>
              <div class="profile-giorno-ricorrenze">
                @foreach ($ricorrenzeDelGiorno as $ricorrenza)
                  <article
                    class="profile-ricorrenza profile-ricorrenza-{{ $ricorrenza->tipo->value }}"
                    data-tipo="{{ $ricorrenza->tipo->value }}"
                  >
                    <div class="profile-ricorrenza-icona" aria-hidden="true">
                      <svg
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      >
                        @switch ($ricorrenza->tipo)
                          @case (TipoRicorrenza::Matrimonio)
                            <circle cx="9" cy="14" r="5.5" />
                            <circle cx="15" cy="14" r="5.5" />
                            <path d="M10.5 3.5h3l-1.5 2.5z" />
                            @break
                          @case (TipoRicorrenza::Entrata)
                            <path d="M3 10.5 12 3l9 7.5" />
                            <path d="M5 9.5V21h14V9.5" />
                            <path d="M10 21v-6h4v6" />
                            @break
                          @case (TipoRicorrenza::MammaDiVocazione)
                            <path
                              d="M12 20s-7.5-4.6-7.5-10.2A4.3 4.3 0 0 1 12 7.2a4.3 4.3 0 0 1 7.5 2.6C19.5 15.4 12 20 12 20z"
                            />
                            @break
                          @case (TipoRicorrenza::Sacerdozio)
                            <path
                              d="M6.5 3h11c0 5.5-2.4 8.5-5.5 8.5S6.5 8.5 6.5 3z"
                            />
                            <path d="M12 11.5V18" />
                            <path d="M8 21h8" />
                            <path d="M12 5v3.5M10.3 6.7h3.4" />
                            @break
                          @case (TipoRicorrenza::Decesso)
                            <path
                              d="M12 2.5c1.6 2 1.6 3.3 0 4.8-1.6-1.5-1.6-2.8 0-4.8z"
                            />
                            <path d="M12 7.3V9" />
                            <rect x="8.5" y="9" width="7" height="12" rx="1" />
                            @break

                        @endswitch
                      </svg>
                    </div>
                    <div
                      class="flex-grow-1 d-flex flex-column gap-1"
                      style="min-width: 0"
                    >
                      <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="profile-event-label">
                          {{ $ricorrenza->tipo->nome() }}
                        </span>
                        @if ($ricorrenza->giubileo())
                          <span class="profile-giubileo">
                            {{ $ricorrenza->giubileo() }}
                          </span>
                        @endif
                      </div>
                      <h3 class="h6 fw-semibold mb-0">
                        {{ $ricorrenza->titolo() }}
                      </h3>
                      <div class="d-flex flex-wrap gap-1">
                        @foreach ($ricorrenza->persone as $persona)
                          <a
                            class="profile-ricorrenza-persona"
                            href="{{ route("nomadelfia.person.profile", $persona->id) }}"
                          >
                            <span
                              class="profile-person-avatar {{ $persona->isMaschio() ? "profile-person-m" : "profile-person-f" }}"
                              aria-hidden="true"
                            >
                              {{ mb_substr($persona->nome, 0, 1) }}{{ mb_substr($persona->cognome, 0, 1) }}
                            </span>
                            {{ $persona->nominativo }}
                          </a>
                        @endforeach
                      </div>
                      <small class="text-body-secondary">
                        {{ $ricorrenza->dettaglio() }}
                      </small>
                    </div>
                    <div class="profile-ricorrenza-anni">
                      <b>{{ $ricorrenza->anni }}</b>
                      <small
                        >{{ $ricorrenza->anni === 1 ? "anno" : "anni" }}</small
                      >
                    </div>
                  </article>
                @endforeach
              </div>
            </li>
          @endforeach
        </ol>
      </section>

      <aside class="col-lg-4" aria-labelledby="titolo-giubilei">
        <h2 id="titolo-giubilei" class="h5 mb-3">Giubilei fino a fine anno</h2>
        @if ($giubilei->isEmpty())
          <p class="text-body-secondary">Nessun giubileo fino a fine anno.</p>
        @else
          <ul class="profile-kv card list-unstyled mb-0">
            @foreach ($giubilei as $giubileo)
              <li
                class="profile-giubileo-row profile-ricorrenza-{{ $giubileo->tipo->value }}"
              >
                <div class="profile-giubileo-data">
                  <b>{{ $giubileo->data->day }}</b>
                  <small>{{ $giubileo->data->translatedFormat("M") }}</small>
                </div>
                <div style="min-width: 0">
                  <div class="d-flex align-items-center gap-2 fw-semibold">
                    <i class="profile-dot-ricorrenza" aria-hidden="true"></i>
                    {{ $giubileo->giubileo() }} &middot; {{ $giubileo->tipo->nome() }}
                  </div>
                  <div class="small text-body-secondary">
                    {{ $giubileo->persone->pluck("nominativo")->join(", ", " e ") }}
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        @endif
      </aside>
    </div>
  </div>

  <script>
    // No tipo selected shows all the ricorrenze, otherwise only the ones of the selected tipi
    document.querySelectorAll(".profile-filter").forEach(function (filtro) {
      filtro.addEventListener("click", function () {
        filtro.setAttribute(
          "aria-pressed",
          filtro.getAttribute("aria-pressed") === "true" ? "false" : "true",
        );
        const scelti = Array.from(
          document.querySelectorAll('.profile-filter[aria-pressed="true"]'),
        ).map(function (scelto) {
          return scelto.dataset.tipo;
        });
        document
          .querySelectorAll(".profile-ricorrenza[data-tipo]")
          .forEach(function (ricorrenza) {
            ricorrenza.hidden =
              scelti.length > 0 && !scelti.includes(ricorrenza.dataset.tipo);
          });

        let visibili = 0;
        document.querySelectorAll(".profile-giorno").forEach(function (giorno) {
          const conRicorrenze =
            giorno.querySelector(".profile-ricorrenza:not([hidden])") !== null;
          giorno.hidden = !conRicorrenze;
          visibili += conRicorrenze ? 1 : 0;
        });
        document.getElementById("ricorrenze-nascoste").hidden =
          visibili > 0 || document.querySelector(".profile-giorno") === null;
      });
    });
  </script>
@endsection
