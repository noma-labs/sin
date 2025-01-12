@extends("officina.index")

@section("title", "Filtri")

@section("content")
    @include("partials.header", ["title" => "Gestione Filtri"])

    <div class="row">
        <div class="col-md-8">
            <table class="table table-hover table-bordered table-sm">
                <thead class="thead-inverse">
                    <tr>
                        <th width="30%">Codice</th>
                        <th width="10%">Tipo</th>
                        <th width="30%">Note</th>
                        <th width="20%">Operazioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($filtri as $filtro)
                        <tr>
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
                                                class="btn btn-danger btn-sm btn-block"
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
