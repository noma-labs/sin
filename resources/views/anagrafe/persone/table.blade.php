<div class="table-responsive">
  <table class="table table-hover">
    <thead>
      <tr class="table-warning">
        <th>Numero Elenco</th>
        <th>Nominativo</th>
        <th>Nome</th>
        <th>Cognome</th>
        <th>Data Nascita</th>
        <th>Luogo di Nascita</th>
        <th>Famiglia</th>
        <th>Stato</th>
        <th>Operazioni</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($persone as $persona)
        <tr class="table-primary" hoverable>
          <td>
            <span class="badge bg-success">
              {{ $persona->numero_elenco }}
            </span>
          </td>
          <td>{{ $persona->nominativo }}</td>
          <td>{{ $persona->nome }}</td>
          <td>{{ $persona->cognome }}</td>
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
                href="{{ route("nomadelfia.person.show", ["id" => $persona->id]) }}"
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
