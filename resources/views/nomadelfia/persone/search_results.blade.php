@extends("nomadelfia.index")

@section("title", "Ricerca Persone")

@section("content")
    @include("partials.header", ["title" => "Ricerca Persone"])

    @include("nomadelfia.persone.search_form")

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        Ricerca effettuata:
        <strong>{{ $msgSearch }}</strong>
        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>

    <div id="results" class="alert alert-success">
        Numero di persone trovate:
        <strong>{{ $persone->total() }}</strong>
    </div>

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
                    <th>Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($persone as $persona)
                    @empty($persona->delated_at)
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
                    @endempty
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-6 offset-md-4">
            {{ $persone->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
        </div>
    </div>
@endsection
