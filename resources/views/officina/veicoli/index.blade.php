@extends("officina.index")
@section("title", "Veicoli")

@section("content")
    @include("partials.header", ["title" => "Gestione Veicoli (" . App\Officina\Models\Veicolo::count() . ")"])

    <div class="card mb-3">
        <div class="card-header">
            <h3>Ricerca</h3>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="row">
                    <div class="col col-md-2">
                        <label class="form-label" for="nome">Nome</label>
                        <input
                            type="text"
                            name="nome"
                            id="nome"
                            class="form-control"
                            placeholder="Nome"
                        />
                    </div>
                    <div class="col col-md-2">
                        <label class="form-label" for="targa">Targa</label>
                        <input
                            type="text"
                            name="targa"
                            id="targa"
                            class="form-control"
                            placeholder="Targa"
                        />
                    </div>
                    <div class="col col-md-2">
                        <label class="form-label" for="marca">Marca</label>
                        <select name="marca" id="marca" class="form-select">
                            <option value="">--Marche--</option>
                            @foreach ($marche as $marca)
                                <option value="{{ $marca->id }}">
                                    {{ $marca->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col col-md-2">
                        <label class="form-label" for="modello">Modello</label>
                        <select name="modello" id="modello" class="form-select">
                            <option value="">--Modelli--</option>
                            @foreach ($modelli as $modello)
                                <option value="{{ $modello->id }}">
                                    {{ $modello->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col d-flex align-items-end">
                        <button class="btn btn-primary" type="submit">
                            Cerca
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
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
                    <th>Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($veicoli as $veicolo)
                    <tr class="table-primary" hoverable>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $veicolo->nome }}
                            @if ($veicolo->prenotabile)
                                <span class="badge bg-success">
                                    prenotabile
                                </span>
                            @endif
                        </td>
                        <td>{{ $veicolo->targa }}</td>
                        <td>{{ $veicolo->modello->marca->nome }}</td>
                        <td>{{ $veicolo->modello->nome }}</td>
                        <td>{{ $veicolo->impiego->nome }}</td>
                        <td>{{ $veicolo->tipologia->nome }}</td>
                        <td>{{ $veicolo->alimentazione->nome }}</td>
                        <td>{{ $veicolo->num_posti }}</td>
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
