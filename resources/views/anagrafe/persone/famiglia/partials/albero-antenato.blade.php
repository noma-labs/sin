{{-- The genitori of a genitore above him, the nonni of the persona, with the line down to the box of the genitori --}}
@if ($antenato->genitori === null || $antenato->genitori->isEmpty())
  <div class="albero-segnaposto">Genitori non registrati</div>
@else
  <div
    class="albero-coppia {{ $antenato->genitori->count() > 1 ? "albero-sposi" : "" }}"
  >
    @foreach ($antenato->genitori as $nonno)
      @if (! $loop->first)
        @include("anagrafe.persone.famiglia.partials.albero-anello", ["numero" => null, "anno" => null, "casa" => false])
      @endif

      @include("anagrafe.persone.famiglia.partials.albero-scheda", ["componente" => $nonno])
    @endforeach
  </div>
@endif

<span class="albero-filo" aria-hidden="true"></span>
<div class="albero-etichetta">nonni di {{ $persona->nome }}</div>
<div class="albero-gomito" aria-hidden="true"><span></span><span></span></div>
