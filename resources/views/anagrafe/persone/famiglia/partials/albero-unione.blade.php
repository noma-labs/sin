{{-- An unione of a persona: the anello next to her and the coniuge, or the famiglia itself when it has no coniuge (a mamma di vocazione). On the left of the persona ($lato "sx") the order is mirrored. --}}
@php
  $famiglia = $ramoFamiglia->famiglia;
  $anno = $famiglia->data_creazione ? Carbon::parse($famiglia->data_creazione)->year : null;
@endphp

@if ($lato === "dx")
  @include("anagrafe.persone.famiglia.partials.albero-anello", ["numero" => $totale > 1 ? $numero : null, "anno" => $anno, "casa" => ! $ramoFamiglia->coniuge])
@endif

@if ($ramoFamiglia->coniuge)
  @include("anagrafe.persone.famiglia.partials.albero-scheda", ["componente" => $ramoFamiglia->coniuge, "io" => false, "badge" => null])
@else
  <a
    class="albero-scheda albero-scheda-famiglia"
    href="{{ route("anagrafe.families.show", $famiglia->id) }}"
  >
    <span class="albero-scheda-avatar albero-scheda-casa" aria-hidden="true">
      <svg
        width="26"
        height="26"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M3 11l9-7 9 7"></path>
        <path d="M5 10v10h14V10"></path>
      </svg>
    </span>
    <span class="albero-scheda-nome">
      {{ $famiglia->tipo?->nome() ?? "Famiglia" }}
    </span>
    <span class="albero-scheda-anni">{{ $famiglia->nome_famiglia }}</span>
  </a>
@endif

@if ($lato === "sx")
  @include("anagrafe.persone.famiglia.partials.albero-anello", ["numero" => $totale > 1 ? $numero : null, "anno" => $anno, "casa" => ! $ramoFamiglia->coniuge])
@endif
