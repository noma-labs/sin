{{-- A persona in the albero, with $badge as a note on the persona --}}
@php
  $nominativo = \App\View\Components\PersonaNome::nominativoDiverso($componente);
@endphp

<a
  class="albero-scheda @if ($io ?? false) albero-scheda-io @endif"
  href="{{ route("nomadelfia.person.profile", $componente->id) }}"
>
  <x-persona-avatar :persona="$componente" size="md" />
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
