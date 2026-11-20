@extends("nomadelfia.index")

@section("title", "Famiglia " . $famiglia->nome_famiglia)

@section("content")
  @use("App\Anagrafe\Famiglia\Models\TipoFamiglia")

  @php
    $isConiugi = $famiglia->tipo === null || $famiglia->tipo === TipoFamiglia::Coniugi;
    $ruoloGenitore = fn ($genitore) => match (true) {
        $genitore->pivot->posizione_famiglia === "MOGLIE" => "Moglie",
        $isConiugi => "Marito",
        default => $famiglia->tipo->nome(),
    };
    $ruoloFiglio = fn ($figlio) => Illuminate\Support\Str::ucfirst(mb_strtolower($figlio->pivot->posizione_famiglia));
  @endphp

  <div class="person-profile">
    <nav aria-label="Percorso di navigazione">
      <ol class="breadcrumb small mb-2">
        <li class="breadcrumb-item">
          <a href="{{ route("anagrafe.families") }}">Famiglie</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          {{ $famiglia->nome_famiglia }}
        </li>
      </ol>
    </nav>

    <header
      class="profile-band profile-band-solo profile-band-famiglia rounded-3 mb-4"
    >
      <div class="d-flex flex-wrap gap-3 align-items-start">
        <div class="profile-avatar profile-avatar-famiglia" aria-hidden="true">
          <svg
            width="44"
            height="44"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M3 10.5 12 3l9 7.5" />
            <path d="M5 9.5V21h14V9.5" />
            <path d="M10 21v-6h4v6" />
          </svg>
        </div>
        <div class="flex-grow-1" style="min-width: 0">
          <div class="profile-occhiello">Famiglia</div>
          <h1 class="profile-name mb-0">
            Famiglia {{ $famiglia->nome_famiglia }}
          </h1>
          <div class="profile-muted mb-1">
            {{ $famiglia->tipo?->nome() ?? "Tipo non indicato" }}
          </div>
          <div class="mb-2">
            <span class="profile-label">
              {{ $famiglia->tipo?->etichettaDataCreazione() ?? "Data creazione" }}
            </span>
            @if ($famiglia->data_creazione)
              {{ Carbon::parse($famiglia->data_creazione)->translatedFormat("j M Y") }}
            @else
              non conosciuta
            @endif
          </div>
          <div class="d-flex flex-wrap gap-2">
            @if ($inNomadelfia)
              <span class="profile-chip profile-chip-presente">
                In Nomadelfia
              </span>
            @else
              <span class="profile-chip">Fuori da Nomadelfia</span>
            @endif
            @if ($gruppoAttuale)
              <span class="profile-chip">
                Gruppo {{ $gruppoAttuale->nome }}
              </span>
            @endif
            <span class="profile-chip">
              {{ $componentiNelNucleo->count() }} nel nucleo
            </span>
          </div>
        </div>
        @can("popolazione.persona.modifica")
          <div class="d-flex flex-wrap gap-2">
            @include("nomadelfia.templates.aggiornaFamiglia", ["famiglia" => $famiglia, "buttonStyle" => "profile-btn"])
            @include("nomadelfia.templates.aggiungiComponente", ["famiglia" => $famiglia, "buttonStyle" => "btn-success"])
            @if ($inNomadelfia)
              <x-modal
                modal-title="Uscita famiglia dalla comunità"
                button-title="Uscita famiglia"
                button-style="btn-outline-danger"
              >
                <x-slot:body>
                  <form
                    class="form"
                    method="POST"
                    id="formUscitaFamiglia{{ $famiglia->id }}"
                    action="{{ route("nomadelfia.family.leave", ["id" => $famiglia->id]) }}"
                  >
                    @csrf
                    <p>
                      Inserire la data di uscita della famiglia {{ $famiglia->nome_famiglia }}
                    </p>
                    <input
                      class="form-control"
                      type="date"
                      name="data_uscita"
                    />
                  </form>
                </x-slot:body>
                <x-slot:footer>
                  <button
                    class="btn btn-success"
                    form="formUscitaFamiglia{{ $famiglia->id }}"
                  >
                    Salva
                  </button>
                </x-slot:footer>
              </x-modal>
            @endif
          </div>
        @endcan
      </div>
    </header>

    <div class="row g-4">
      <div class="col-lg-8 d-flex flex-column gap-4">
        <section aria-labelledby="titolo-genitori">
          <h2 id="titolo-genitori" class="h5 mb-3">
            {{ $isConiugi ? "Coniugi" : "Capofamiglia" }}
            <span class="badge rounded-pill text-bg-light">
              {{ $genitori->count() }}
            </span>
          </h2>
          @if ($genitori->isEmpty())
            <p class="text-body-secondary">Nessun capofamiglia</p>
          @else
            <div class="albero-coppia albero-sposi famiglia-coppia">
              @php
                $annoFamiglia = $famiglia->data_creazione ? Carbon::parse($famiglia->data_creazione)->year : null;
              @endphp

              @if (! $isConiugi)
                @include("anagrafe.persone.famiglia.partials.albero-anello", ["numero" => null, "anno" => $annoFamiglia, "casa" => true])
              @endif

              @foreach ($genitori as $componente)
                @if (! $loop->first)
                  @include("anagrafe.persone.famiglia.partials.albero-anello", ["numero" => null, "anno" => $annoFamiglia, "casa" => false])
                @endif

                <div
                  class="famiglia-coppia-membro d-flex align-items-start gap-2"
                >
                  <div class="flex-grow-1" style="min-width: 0">
                    @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $componente, "ruolo" => $ruoloGenitore($componente), "indentato" => false, "fuoriNucleo" => $componente->pivot->stato !== "1"])
                  </div>
                  @can("popolazione.persona.modifica")
                    @include("nomadelfia.templates.aggiornaComponente", ["componente" => $componente, "famiglia" => $famiglia])
                  @endcan
                </div>
              @endforeach
            </div>
          @endif
        </section>

        @foreach ([
            ["id" => "figli", "titolo" => "Figli nel nucleo", "persone" => $figliNelNucleo, "ruolo" => $ruoloFiglio, "vuoto" => "Nessun figlio nel nucleo"],
            ["id" => "figli-fuori", "titolo" => "Figli non più nel nucleo", "persone" => $figliFuoriNucleo, "ruolo" => $ruoloFiglio, "vuoto" => null],
          ]
          as $gruppo)
          @if ($gruppo["persone"]->isNotEmpty() || $gruppo["vuoto"])
            <section aria-labelledby="titolo-{{ $gruppo["id"] }}">
              <h2 id="titolo-{{ $gruppo["id"] }}" class="h5 mb-3">
                {{ $gruppo["titolo"] }}
                <span class="badge rounded-pill text-bg-light">
                  {{ $gruppo["persone"]->count() }}
                </span>
              </h2>
              @forelse ($gruppo["persone"] as $componente)
                <div class="d-flex align-items-start gap-2">
                  <div class="flex-grow-1" style="min-width: 0">
                    @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $componente, "ruolo" => $gruppo["ruolo"]($componente), "indentato" => false, "fuoriNucleo" => $componente->pivot->stato !== "1"])
                  </div>
                  @can("popolazione.persona.modifica")
                    @include("nomadelfia.templates.aggiornaComponente", ["componente" => $componente, "famiglia" => $famiglia])
                    @if (in_array($componente->pivot->posizione_famiglia, ["FIGLIO NATO", "FIGLIO ACCOLTO", "AGGREGATO"], true))
                      @include("nomadelfia.templates.rimuoviFiglio", ["figlio" => $componente, "famiglia" => $famiglia])
                    @endif
                  @endcan
                </div>
              @empty
                <p class="text-body-secondary">{{ $gruppo["vuoto"] }}</p>
              @endforelse
            </section>
          @endif
        @endforeach
      </div>

      <div class="col-lg-4 d-flex flex-column gap-4">
        <section aria-labelledby="titolo-gruppo">
          <h2 id="titolo-gruppo" class="h5 mb-3">Gruppo familiare</h2>
          <dl class="profile-kv card mb-0">
            <div class="profile-kv-row">
              <dt>Attuale</dt>
              <dd>
                @if ($gruppoAttuale)
                  @include("nomadelfia.templates.gruppo", ["id" => $gruppoAttuale->id, "nome" => $gruppoAttuale->nome])
                  <small>dal {{ $gruppoAttuale->data_entrata_gruppo }}</small>
                @else
                  <span class="text-body-secondary">Nessun gruppo</span>
                @endif
              </dd>
              <span>
                @if ($gruppoAttuale)
                  @can("popolazione.persona.modifica")
                    @include("nomadelfia.templates.spostaFamigliaNuovoGruppo", ["famiglia_id" => $famiglia->id, "componenti" => $componentiNelNucleo, "gruppo_id" => $gruppoAttuale->id, "buttonStyle" => "btn-sm profile-btn"])
                  @endcan
                @endif
              </span>
            </div>
            @foreach ($gruppiStorici as $gruppo)
              <div class="profile-kv-row">
                <dt>Precedente</dt>
                <dd>
                  @include("nomadelfia.templates.gruppo", ["id" => $gruppo->id, "nome" => $gruppo->nome])
                  <small>
                    {{ $gruppo->data_entrata_gruppo }} &ndash; {{ $gruppo->data_uscita_gruppo }}
                  </small>
                </dd>
                <span></span>
              </div>
            @endforeach
          </dl>
        </section>
      </div>
    </div>
  </div>
@endsection
