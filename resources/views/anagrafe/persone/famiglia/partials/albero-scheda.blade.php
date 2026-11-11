{{-- A persona in the albero, with $badge as a note on the persona --}}
@php
  $nominativo = \App\View\Components\PersonaNome::nominativoDiverso($componente);
@endphp

<a
  class="albero-scheda @if ($io ?? false) albero-scheda-io @endif"
  href="{{ route("nomadelfia.person.profile", $componente->id) }}"
>
  <span
    class="albero-scheda-avatar {{ $componente->isMaschio() ? "profile-person-m" : "profile-person-f" }}"
    aria-hidden="true"
  >
    {{ mb_substr($componente->nome, 0, 1) }}{{ mb_substr($componente->cognome, 0, 1) }}
  </span>
  <span class="albero-scheda-nome">
    {{ $componente->nome }}
    <span class="d-block">{{ $componente->cognome }}</span>
  </span>
  @if ($nominativo)
    <span class="albero-scheda-anni">({{ $nominativo }})</span>
  @endif

  <span class="albero-scheda-anni">
    {{ $componente->data_nascita ? Carbon::parse($componente->data_nascita)->year : "" }}
    @if ($componente->data_decesso)
      – {{ Carbon::parse($componente->data_decesso)->year }}
    @endif
  </span>
  @if (! empty($badge))
    <span class="albero-scheda-badge">{{ $badge }}</span>
  @endif
</a>
