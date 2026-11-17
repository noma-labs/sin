@can("popolazione.persona.visualizza")
  <a href="{{ route("nomadelfia.person.profile", $persona->id) }}">
    <x-persona-nome :persona="$persona" />
  </a>
@else
  <x-persona-nome :persona="$persona" />
@endcan
