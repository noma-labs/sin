@if ($persona->avatar_path)
  <img
    src="{{ route("anagrafe.person.avatar.show", $persona->id) }}"
    alt="Foto di {{ $persona->nominativo }}"
  />
@else
  <span aria-hidden="true">
    {{ mb_substr($persona->nome, 0, 1) }}{{ mb_substr($persona->cognome, 0, 1) }}
  </span>
@endif
