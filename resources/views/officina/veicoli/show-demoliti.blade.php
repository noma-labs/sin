@extends("officina.index")
@section("title", "Veicoli")

@section("content")
    @include("partials.header", ["title" => "Gestione Veicoli Demoliti (" . $veicoli->count() . ")"])

    <div class="card mb-3">
        <div class="card-header">
            <h3>Ricerca</h3>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <label for="nome">Nome</label>
                <input
                    type="text"
                    name="nome"
                    id="nome"
                    class="form-control"
                    placeholder="Nome"
                />

                <label for="targa">Targa</label>
                <input
                    type="text"
                    name="targa"
                    id="targa"
                    class="form-control"
                    placeholder="Targa"
                />

                <label for="marca">Marca</label>
                <select name="marca" id="marca" class="form-select">
                    <option value="">--Marche--</option>
                    @foreach ($marche as $marca)
                        <option value="{{ $marca->id }}">
                            {{ $marca->nome }}
                        </option>
                    @endforeach
                </select>

                <label for="modello">Modello</label>
                <select name="modello" id="modello" class="form-select">
                    <option value="">--Modelli--</option>
                    @foreach ($modelli as $modello)
                        <option value="{{ $modello->id }}">
                            {{ $modello->nome }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-primary" type="submit">Cerca</button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="table-warning">
                    <th>#</th>
                    <th>Nome</th>
                    <th>Targa</th>
                    <th>Marca</th>
                    <th>Modello</th>
                    <th>Impiego</th>
                    <th>Tipologia</th>
                    <th>Alimentazione</th>
                    <th>Posti</th>
                    <th>Demolito il</th>
                    <th>Operazioni</th>
                </tr>
            </thead>
           <tbody>
                @foreach ($veicoli as $veicolo)
                    <tr  class="table-primary" hoverable>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $veicolo->nome }}</td>
                        <td>{{ $veicolo->targa }}</td>
                        <td>{{ $veicolo->modello->marca->nome }}</td>
                        <td>{{ $veicolo->modello->nome }}</td>
                        <td>{{ $veicolo->impiego->nome }}</td>
                        <td>{{ $veicolo->tipologia->nome }}</td>
                        <td>{{ $veicolo->alimentazione->nome }}</td>
                        <td>{{ $veicolo->num_posti }}</td>
                        <td>{{ $veicolo->deleted_at }}</td>
                        <td>
                            <a
                                class="btn btn-warning btn-sm"
                                href="{{ route("veicoli.dettaglio", ["id" => $veicolo->id]) }}"
                            >
                                Dettagli
                            </a>
                            <a
                                class="btn btn-success btn-sm"
                                href="{{ route("veicoli.modifica", ["id" => $veicolo->id]) }}"
                            >
                                Modifica
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
