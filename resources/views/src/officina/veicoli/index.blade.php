@extends("officina.index")
@section("title", "Veicoli")

@section("content")
    <div class="my-page-title">
        <div class="d-flex justify-content-end">
            <div class="me-auto p-2">
                <span class="h1 text-center">Gestione Veicoli</span>
            </div>
            <div class="p-2 text-end">
                <h5 class="m-1">
                    {{ App\Officina\Models\Veicolo::count() }} veicoli
                </h5>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Ricerca</h3>
        </div>
        <div class="card-body">
            <form action="" method="get" class="d-flex align-items-center">
                <label for="nome" class="visually-hidden">Nome</label>
                <input
                    type="text"
                    name="nome"
                    id="nome"
                    class="form-control"
                    placeholder="Nome"
                />

                <label for="targa" class="visually-hidden">Targa</label>
                <input
                    type="text"
                    name="targa"
                    id="targa"
                    class="form-control ms-3"
                    placeholder="Targa"
                />

                <label for="marca" class="visually-hidden">Marca</label>
                <select name="marca" id="marca" class="form-select ms-3">
                    <option value="">--Marche--</option>
                    @foreach ($marche as $marca)
                        <option value="{{ $marca->id }}">
                            {{ $marca->nome }}
                        </option>
                    @endforeach
                </select>

                <label for="modello" class="visually-hidden">Modello</label>
                <select name="modello" id="modello" class="form-select ms-3">
                    <option value="">--Modelli--</option>
                    @foreach ($modelli as $modello)
                        <option value="{{ $modello->id }}">
                            {{ $modello->nome }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-primary ms-3" type="submit">
                    Cerca
                </button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table
            class="table table-hover table-bordered"
            style="overflow-x: scroll; table-layout: auto"
        >
            <thead class="thead-inverse">
                <tr>
                    <th width="2%">#</th>
                    <th width="20%">Nome</th>
                    <th width="12%">Targa</th>
                    <th width="9%">Marca</th>
                    <th width="9%">Modello</th>
                    <th width="11%">Impiego</th>
                    <th width="8%">Tipologia</th>
                    <th width="8%">Alimentazione</th>
                    <th width="5%">Posti</th>
                    <th width="16%">Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($veicoli as $veicolo)
                    <tr hoverable>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $veicolo->nome }}
                            @if ($veicolo->prenotabile)
                                <span class="badge text-bg-success">
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
