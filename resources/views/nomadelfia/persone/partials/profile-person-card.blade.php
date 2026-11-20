@php
  $nascitaComponente = Carbon::parse($componente->data_nascita);
@endphp

<a
  class="profile-person @if ($indentato) profile-person-indentato @endif @if ($fuoriNucleo ?? false) profile-person-fuori-nucleo @endif"
  href="{{ route("nomadelfia.person.profile", $componente->id) }}"
>
  <x-persona-avatar :persona="$componente" size="sm" />
  <span class="flex-grow-1" style="min-width: 0">
    <span class="d-block fw-semibold text-truncate">
      <x-persona-nome :persona="$componente" />
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
