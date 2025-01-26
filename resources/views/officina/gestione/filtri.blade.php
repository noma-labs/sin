@extends("officina.index")

@section("title", "Filtri")

@section("content")
    @include("partials.header", ["title" => "Gestione Filtri"])

    <div class="d-flex justify-content-end mb-3">
        @include("officina.veicoli.aggiungiFiltro")
    </div>
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr class="table-warning">
                        <th>Codice</th>
                        <th>Tipo</th>
                        <th>Note</th>
                        <th>Operazioni</th>
                    </tr>
                </thead>
               <tbody>
                    @foreach ($filtri as $filtro)
                        <tr class="table-primary">
                            <td>{{ $filtro->codice }}</td>
                            <td>{{ $filtro->tipo }}</td>
                            <td>{{ $filtro->note }}</td>
                            <td>
                                <div class="row">
                                    <div class="col">
                                        <form
                                            action="{{ route("filtri.delete", $filtro->id) }}"
                                            method="post"
                                        >
                                            @csrf
                                            @method("DELETE")
                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm"
                                            >
                                                Elimina
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
