@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Popolazione", "subtitle" => $population->count()])
    <table class="table table-hover">
        <thead>
            <tr class="table-warning">
                <th>Numero Elenco</th>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Data Nascita</th>
                <th>Sesso</th>
                <th>Provincia Nascita</th>
                <th>Posizione</th>
                <th>Gruppo</th>
                <th>Famiglia</th>
                <th>Azienda</th>
              </tr>
        </thead>

          <tbody>
            @foreach($population as $persona)
              <tr class="table-primary">
                <td>{{ $persona->numero_elenco }} </td>
                <td>{{ $persona->nome }} </td>
                <td>{{ $persona->cognome }} </td>
                <td>{{ $persona->data_nascita }} </td>
                <td>{{ $persona->sesso }} </td>
                <td>{{ $persona->provincia_nascita }} </td>
                <td>{{ $persona->posizione }} </td>
                <td>{{ $persona->gruppo }} </td>
                <td>{{ $persona->famiglia }} </td>
                <td>{{ $persona->azienda }} </td>
              </tr>
              @endforeach
          </tbody>

    </table>

@endsection
