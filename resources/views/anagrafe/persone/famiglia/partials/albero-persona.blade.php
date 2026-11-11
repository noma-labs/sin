{{-- A persona with her unioni: the first one on the right, the second on the left and so on, so that the persona stays in the middle --}}
@php
  $io = $ramo->persona->id === $persona->id;
  $totale = count($ramo->famiglie);
  $unioni = collect($ramo->famiglie)->map(fn ($ramoFamiglia, $indice) => ["ramo" => $ramoFamiglia, "numero" => $indice + 1]);
  $destra = $unioni->filter(fn ($unione) => $unione["numero"] % 2 === 1);
  $sinistra = $unioni->filter(fn ($unione) => $unione["numero"] % 2 === 0)->reverse();
  $spaziatori = $totale > 1 ? $destra->count() - $sinistra->count() : 0;
  $origini = $io ? $albero->percorso->count() : 0;
@endphp

<div class="albero-coppia {{ $totale > 0 ? "albero-sposi" : "" }}">
  @for ($i = 0; $i < $spaziatori; $i++)
    <span class="albero-spaziatore" aria-hidden="true"></span>
  @endfor

  @foreach ($sinistra as $unione)
    @include("anagrafe.persone.famiglia.partials.albero-unione", ["ramoFamiglia" => $unione["ramo"], "numero" => $unione["numero"], "totale" => $totale, "lato" => "sx"])
  @endforeach

  @include("anagrafe.persone.famiglia.partials.albero-scheda", ["componente" => $ramo->persona, "io" => $io, "badge" => $origini > 1 ? $origini . " famiglie d'origine" : null])
  @foreach ($destra as $unione)
    @include("anagrafe.persone.famiglia.partials.albero-unione", ["ramoFamiglia" => $unione["ramo"], "numero" => $unione["numero"], "totale" => $totale, "lato" => "dx"])
  @endforeach
</div>

@foreach ($unioni as $unione)
  @php
    $ramoFamiglia = $unione["ramo"];
    $numeroFigli = $ramoFamiglia->figli->count();
    $titolo = $numeroFigli . ($numeroFigli === 1 ? " figlio di " : " figli di ") . $ramo->persona->nome . ($ramoFamiglia->coniuge ? " e " . $ramoFamiglia->coniuge->nome : "");
  @endphp

  <details class="albero-figli" @if ($io) open @endif>
    <summary>
      @if ($totale > 1)
        <span class="albero-numero">{{ $unione["numero"] }}</span>
      @endif

      {{ $titolo }}
      <small>
        {{ $ramoFamiglia->famiglia->nome_famiglia }}
        @if ($ramoFamiglia->famiglia->tipo && ! $ramoFamiglia->coniuge)
          &middot; {{ $ramoFamiglia->famiglia->tipo->nome() }}
        @endif
      </small>
    </summary>
    @if ($ramoFamiglia->figli->isNotEmpty())
      <div class="albero-figli-elenco">
        @foreach ($ramoFamiglia->figli as $figlio)
          @php
            $vicine = $ramoFamiglia->famiglieVicine($figlio);
          @endphp

          <div>
            @include("nomadelfia.persone.partials.profile-person-card", ["componente" => $figlio, "ruolo" => Illuminate\Support\Str::ucfirst(mb_strtolower($figlio->pivot->posizione_famiglia)), "indentato" => false])
            @if ($vicine["prima"])
              <span class="albero-famiglia-vicina">
                <span aria-hidden="true">↩</span>
                prima:
                <a
                  href="{{ route("anagrafe.families.show", $vicine["prima"]->id) }}"
                >
                  {{ $vicine["prima"]->nome_famiglia }}
                </a>
                ({{ \App\Anagrafe\Famiglia\DataTransferObjects\AlberoFamiliare::etichettaTappa($figlio, $vicine["prima"]->pivot->posizione_famiglia, $vicine["prima"]->pivot->data_entrata) }})
              </span>
            @endif

            @if ($vicine["poi"])
              <span class="albero-famiglia-vicina">
                <span aria-hidden="true">↪</span>
                poi:
                <a
                  href="{{ route("anagrafe.families.show", $vicine["poi"]->id) }}"
                >
                  {{ $vicine["poi"]->nome_famiglia }}
                </a>
                ({{ \App\Anagrafe\Famiglia\DataTransferObjects\AlberoFamiliare::etichettaTappa($figlio, $vicine["poi"]->pivot->posizione_famiglia, $vicine["poi"]->pivot->data_entrata) }})
              </span>
            @endif

            @foreach ($figlio->famiglieFormate as $famigliaFormata)
              <a
                class="albero-famiglia-formata"
                href="{{ route("anagrafe.families.show", $famigliaFormata->id) }}"
              >
                <span aria-hidden="true">⚭</span>
                {{ $famigliaFormata->nome_famiglia }}
              </a>
            @endforeach
          </div>
        @endforeach
      </div>
    @endif

    <div class="albero-figli-azioni">
      <a
        href="{{ route("anagrafe.families.show", $ramoFamiglia->famiglia->id) }}"
      >
        Apri famiglia
      </a>
    </div>
  </details>
@endforeach
