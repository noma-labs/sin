@extends("nomadelfia.index")

@section("content")
  <div class="person-profile">
    <header class="profile-band profile-band-solo rounded-3 mb-3">
      <h1 class="profile-name mb-0">Albero familiare</h1>
      <div class="profile-muted">
        <a href="{{ route("nomadelfia.person.profile", $persona->id) }}">
          {{ \App\View\Components\PersonaNome::testo($persona) }}
        </a>
        &middot; {{ $attuali->count() + $storico->count() }} {{ $attuali->count() + $storico->count() === 1 ? "famiglia" : "famiglie" }}
      </div>
    </header>

    @if ($attuali->count() > 1)
      <div class="alert alert-warning" role="alert">
        <p>
          {{ $persona->nominativo }} è nel nucleo di {{ $attuali->count() }} famiglie.
          Scegli quale mantenere come famiglia attuale: dalle altre esce dal
          nucleo e restano nel suo storico.
        </p>
        @foreach ($attuali as $attuale)
          <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <a href="{{ route("anagrafe.families.show", $attuale->id) }}">
              {{ $attuale->nome_famiglia }}
            </a>
            <span class="text-body-secondary">
              {{ Illuminate\Support\Str::ucfirst(mb_strtolower($attuale->pivot->posizione_famiglia)) }}
            </span>
            @can("popolazione.persona.modifica")
              <form
                method="POST"
                action="{{ route("anagrafe.person.families.attuale", $persona->id) }}"
              >
                @csrf
                @method("PUT")
                <input
                  type="hidden"
                  name="famiglia_id"
                  value="{{ $attuale->id }}"
                />
                <button
                  type="submit"
                  class="btn btn-sm btn-warning"
                  onclick="return confirm(@js("Mantenere {$attuale->nome_famiglia} come famiglia attuale? Dalle altre famiglie {$persona->nominativo} passa nello storico."))"
                >
                  Mantieni questa
                </button>
              </form>
            @endcan
          </div>
        @endforeach
      </div>
    @endif

    @if ($albero->percorso->count() > 1)
      <nav
        class="albero-percorso"
        aria-label="Famiglie d'origine di {{ $persona->nome }}"
      >
        <span class="profile-label">Famiglie d'origine</span>
        @foreach ($albero->percorso as $tappa)
          @if (! $loop->first)
            <span aria-hidden="true">→</span>
          @endif

          <a
            class="profile-filter"
            href="{{ route("anagrafe.person.families", ["id" => $persona->id, "origine" => $tappa->id]) }}"
            @if ($tappa->id === $albero->origine?->id) aria-current="true" @endif
          >
            {{ $tappa->nome_famiglia . " · " . \App\Anagrafe\Famiglia\DataTransferObjects\AlberoFamiliare::etichettaTappa($persona, $tappa->pivot->posizione_famiglia, $tappa->pivot->data_entrata) }}
          </a>
        @endforeach
      </nav>
    @endif

    <section class="albero-familiare mb-4" aria-label="Albero familiare">
      <div class="albero-tela">
        @if ($albero->genitori !== [])
          <div class="albero-antenati">
            @foreach ($albero->genitori as $antenato)
              <div
                class="albero-lato {{ count($albero->genitori) === 1 ? "albero-lato-solo" : ($loop->first ? "albero-lato-sx" : "albero-lato-dx") }}"
              >
                @include("anagrafe.persone.famiglia.partials.albero-antenato", ["antenato" => $antenato])
              </div>
            @endforeach
          </div>
          <div
            class="albero-coppia {{ count($albero->genitori) > 1 ? "albero-sposi" : "" }}"
          >
            @foreach ($albero->genitori as $antenato)
              @if (! $loop->first)
                @include("anagrafe.persone.famiglia.partials.albero-anello", ["numero" => null, "anno" => $albero->origine?->data_creazione ? Carbon::parse($albero->origine->data_creazione)->year : null, "casa" => false])
              @endif

              @include("anagrafe.persone.famiglia.partials.albero-scheda", ["componente" => $antenato->persona])
            @endforeach
          </div>
        @else
          <div class="albero-segnaposto">Genitori non registrati</div>
        @endif

        <span class="albero-filo" aria-hidden="true"></span>
        <div class="albero-etichetta">
          @if ($albero->origine)
            <a
              href="{{ route("anagrafe.families.show", $albero->origine->id) }}"
            >
              genitori di {{ $persona->nome }}
            </a>
          @else
            genitori di {{ $persona->nome }}
          @endif
        </div>

        <ul class="albero-rami">
          @foreach ($albero->rami as $ramo)
            <li>
              @include("anagrafe.persone.famiglia.partials.albero-persona", ["ramo" => $ramo])
            </li>
          @endforeach
        </ul>
      </div>
    </section>
  </div>
@endsection
