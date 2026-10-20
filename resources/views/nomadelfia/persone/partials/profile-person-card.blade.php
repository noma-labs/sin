@php
  $nascitaComponente = Carbon::parse($componente->data_nascita);
@endphp

<a
  class="profile-person @if ($indentato) profile-person-indentato @endif"
  href="{{ route("nomadelfia.person.profile", $componente->id) }}"
>
  <span
    class="profile-person-avatar {{ $componente->isMaschio() ? "profile-person-m" : "profile-person-f" }}"
    aria-hidden="true"
  >
    {{ mb_substr($componente->nome, 0, 1) }}{{ mb_substr($componente->cognome, 0, 1) }}
  </span>
  <span class="flex-grow-1" style="min-width: 0">
    <span class="d-block fw-semibold text-truncate">
      {{ $componente->nominativo }}
    </span>
    <span class="d-block small text-body-secondary">
      {{ $nascitaComponente->year }}
      @if ($componente->data_decesso)
        – {{ Carbon::parse($componente->data_decesso)->year }}
      @else
        &middot; {{ $nascitaComponente->age }} anni
      @endif
    </span>
  </span>
  <span class="badge rounded-pill text-bg-light">{{ $ruolo }}</span>
</a>
