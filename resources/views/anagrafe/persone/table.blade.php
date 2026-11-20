<div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead>
      <tr class="table-warning">
        <th>Persona</th>
        <th>Numero Elenco</th>
        <th>Data Nascita</th>
        <th>Luogo di Nascita</th>
        <th>Famiglia</th>
        <th>Stato</th>
        <th>Operazioni</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($persone as $persona)
        <tr>
          <td>
            <a
              class="d-flex align-items-center gap-2 text-decoration-none"
              href="{{ route("nomadelfia.person.profile", ["id" => $persona->id]) }}"
            >
              <x-persona-avatar :persona="$persona" size="sm" />
              <x-persona-nome :persona="$persona" />
            </a>
          </td>
          <td>
            @if ($persona->numero_elenco)
              <span class="badge bg-success">
                {{ $persona->numero_elenco }}
              </span>
            @endif
          </td>
          <td>{{ $persona->data_nascita }}</td>
          <td>{{ $persona->provincia_nascita }}</td>
          <td>
            @if ($famiglia = $persona->famiglieAttuali->first())
              <a href="{{ route("anagrafe.families.show", $famiglia->id) }}">
                {{ $famiglia->nome_famiglia }}
              </a>
            @endif
          </td>
          <td>
            @if ($interni->contains($persona->id))
              <span class="badge bg-success">In Nomadelfia</span>
            @endif
            @if ($persona->isDeceduta())
              <span class="badge bg-secondary">Deceduto</span>
            @endif
          </td>
          <td>
            <div class="button-group" role="group">
              <a
                class="btn btn-warning btn-sm"
                href="{{ route("nomadelfia.person.profile", ["id" => $persona->id]) }}"
              >
                Dettaglio
              </a>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="row">
  <div class="col-md-6 offset-md-4">
    {{ $persone->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
  </div>
</div>
