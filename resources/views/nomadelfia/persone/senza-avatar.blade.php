@extends("nomadelfia.index")

@section("title", "Persone senza foto")

@section("content")
  <div class="person-profile">
    <div
      class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3"
    >
      <div style="min-width: 0">
        <h1 class="profile-name mb-1">Persone senza foto</h1>
        <p class="text-body-secondary mb-0">
          @if ($fatte)
            Le persone delle foto che hanno già una foto, dall'ultima salvata.
            Clicca una persona per cambiarla.
          @else
            Le persone senza foto che compaiono nelle foto dell'archivio, prima
            chi ha più foto. Scegli un volto o disegna il riquadro, poi si passa
            alla persona successiva.
          @endif
        </p>
      </div>
      @if (! $fatte && $persone->isNotEmpty())
        <a
          class="btn btn-primary"
          href="{{ route("nomadelfia.person.avatar.volti", ["id" => $persone->first()->id, "coda" => 1]) }}"
        >
          Inizia
        </a>
      @endif
    </div>

    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a
          @class(["nav-link", "active" => ! $fatte])
          @if (! $fatte) aria-current="page" @endif
          href="{{ route("nomadelfia.person.senza-avatar") }}"
        >
          Da fare ({{ $numeroDaFare }})
        </a>
      </li>
      <li class="nav-item">
        <a
          @class(["nav-link", "active" => $fatte])
          @if ($fatte) aria-current="page" @endif
          href="{{ route("nomadelfia.person.senza-avatar", ["stato" => "fatte"]) }}"
        >
          Fatte ({{ $numeroFatte }})
        </a>
      </li>
    </ul>

    @if ($persone->isEmpty())
      <div class="alert alert-light border">
        @if ($fatte)
          Nessuna persona delle foto ha ancora una foto.
        @else
          Tutte le persone delle foto hanno una foto.
        @endif
      </div>
    @elseif ($fatte)
      <div class="avatar-fatte mb-3">
        @foreach ($persone as $persona)
          <a
            class="avatar-fatta"
            href="{{ route("nomadelfia.person.avatar.volti", $persona->id) }}"
          >
            <x-persona-avatar :persona="$persona" size="lg" />
            <span class="avatar-fatta-nome">
              {{ $persona->nome }} {{ $persona->cognome }}
            </span>
            <span class="small text-body-secondary">
              {{ $persona->avatar_aggiornato_il?->format("d/m/Y H:i") ?? "data sconosciuta" }}
            </span>
          </a>
        @endforeach
      </div>
      {{ $persone->links() }}
    @else
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Persona</th>
              <th class="text-end">Foto</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($persone as $persona)
              <tr>
                <td>
                  <a
                    class="d-flex align-items-center gap-2 text-decoration-none"
                    href="{{ route("nomadelfia.person.profile", $persona->id) }}"
                  >
                    <x-persona-avatar :persona="$persona" size="sm" />
                    <x-persona-nome :persona="$persona" />
                  </a>
                </td>
                <td class="text-end">{{ $persona->numero_foto }}</td>
                <td class="text-end">
                  <a
                    class="btn btn-outline-primary btn-sm"
                    href="{{ route("nomadelfia.person.avatar.volti", ["id" => $persona->id, "coda" => 1]) }}"
                  >
                    Scegli la foto
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $persone->links() }}
    @endif
  </div>
@endsection
