@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Persona"])

    <!-- Dati anagrafici -->
    <div class="row">
        @isset($personeEsistenti)
            <div class="col-md-12 table-responsive">
                <table
                    class="table table-hover table-bordered table-sm"
                >
                    <thead class="thead-inverse">
                        <tr>
                            <th>Nominativo</th>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Data Nascita</th>
                            <th>Luogo Nascita</th>
                            <th>sesso</th>
                            <th>Oper.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($personeEsistenti->get() as $persona)
                            <tr hoverable>
                                <td>{{ $persona->nominativo }}</td>
                                <td>{{ $persona->nome }}</td>
                                <td>{{ $persona->cognome }}</td>
                                <td>{{ $persona->data_nascita }}</td>
                                <td>{{ $persona->provincia_nascita }}</td>
                                <td>{{ $persona->sesso }}</td>
                                <td>
                                    <div
                                        class="btn-group"
                                        role="group"
                                        aria-label="Basic example"
                                    >
                                        <a
                                            class="btn btn-success"
                                            href="{{ route("nomadelfia.persone.dettaglio", ["idPersona" => $persona->id]) }}"
                                        >
                                            Scegli
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- fine col existing persone -->
        @endisset
    </div>

    <div class="row">
        <div class="col-md-12">
            <a
                role="button"
                class="btn btn-warning pull-right"
                href="{{ route("nomadelfia.persone.anagrafica.create") }}"
            >
                Aggiungi nuova persona
            </a>
        </div>
    </div>
@endsection
