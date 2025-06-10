@extends("officina.index")

@section("title", "Prenotazioni")

@section("content")
    @include("partials.header", ["title" => "Ricerca Prenotazioni"])

    <form method="GET" action="{{ route("officina.ricerca") }}" class="mb-3">
        @csrf
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cliente</label>
                <select class="form-select" id="cliente" name="cliente_id">
                    <option selected value>--Seleziona--</option>
                    @foreach ($clienti as $cliente)
                        <option
                            value="{{ $cliente->id }}"
                            @if (old('cliente_id') !== null && old('cliente_id') === $cliente->id) selected @endif
                        >
                            {{ $cliente->nominativo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Veicolo</label>
                <select class="form-select" id="veicolo" name="veicolo_id">
                    <option selected value>--Seleziona--</option>
                    @foreach ($veicoli as $veicolo)
                        <option value="{{ $veicolo->id }}">
                            {{ "(" . $veicolo->targa . ") " . $veicolo->nome . " - " . $veicolo->impiego->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Meccanico</label>
                <select class="form-select" id="meccanico" name="meccanico_id">
                    <option selected value>--Seleziona--</option>
                    @foreach ($meccanici as $meccanico)
                        <option value="{{ $meccanico->persona_id }}">
                            {{ $meccanico->nominativo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="uso">Uso</label>
                <select class="form-select" id="uso" name="uso_id" required>
                    <option disabled selected>--Seleziona--</option>
                    @foreach ($usi as $uso)
                        <option value="{{ $uso->ofus_iden }}">
                            {{ $uso->ofus_nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Data partenza</label>
                <select
                    class="form-select"
                    name="criterio_data_partenza"
                    type="text"
                >
                    <option value="<">Minore</option>
                    <option value="<=">Minore Uguale</option>
                    <option value="=">Uguale</option>
                    <option value=">">Maggiore</option>
                    <option value=">=" selected>Maggiore Uguale</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <input type="date" class="form-control" name="data_partenza" />
            </div>

            <div class="col-md-2">
                <label class="form-label">Data arrivo</label>
                <select
                    class="form-select"
                    name="criterio_data_arrivo"
                    type="text"
                >
                    <option value="<">Minore</option>
                    <option value="<=" selected>Minore Uguale</option>
                    <option value="=">Uguale</option>
                    <option value=">">Maggiore</option>
                    <option value=">=">Maggiore Uguale</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <input
                    type="date"
                    class="form-control"
                    id="data_arr"
                    name="data_arrivo"
                />
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    Tutte le prenotazioni nel giorno:
                </label>
                <input
                    type="date"
                    class="form-control"
                    id="data_arr"
                    name="data_singola"
                />
            </div>

            <div class="col-md-5">
                <label for="destinazione">Destinazione</label>
                <input
                    type="text"
                    class="form-control"
                    id="destinazione"
                    name="destinazione"
                />
            </div>

            <div class="col-md-5">
                <label for="note">Note</label>
                <input type="text" class="form-control" id="note" name="note" />
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Ricerca</button>
            </div>
        </div>
    </form>

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
        Numero di prenotazioni trovate:
        @if ($prenotazioni === null || count($prenotazioni) === 0)
            <strong>Nessuna prenotazione trovata</strong>
        @else
            <strong>{{ $prenotazioni->total() }}</strong>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="table-warning">
                    <th>Nome</th>
                    <th>Macchina</th>
                    <th>Data Partenza</th>
                    <th>Ora Partenza</th>
                    <th>Data Arrivo</th>
                    <th>Ora Arrivo</th>
                    <th>Meccanico</th>
                    <th>Uso</th>
                    <th>Destinazione</th>
                    <th>Note</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prenotazioni as $pren)
                    @empty($pren->delated_at)
                        <tr class="table-primary" hoverable>
                            <td>{{ $pren->cliente->nominativo }}</td>
                            <td>
                                {{ $pren->veicolo()->withTrashed()->first()->nome }}
                                @if ($pren->veicolo()->withTrashed()->first()->deleted_at)
                                    <span class="badge bg-danger">
                                        demolito
                                    </span>
                                @endif
                            </td>
                            <td>{{ $pren->data_partenza }}</td>
                            <td>{{ $pren->ora_partenza }}</td>
                            <td>{{ $pren->data_arrivo }}</td>
                            <td>{{ $pren->ora_arrivo }}</td>
                            <td>{{ $pren->meccanico->nominativo }}</td>
                            <td>{{ $pren->uso->ofus_nome }}</td>
                            <td>{{ $pren->destinazione }}</td>
                            <td>{{ $pren->note }}</td>
                            <td>
                                <div class="button-group" role="group">
                                    <a
                                        role="button"
                                        class="btn btn-warning"
                                        href="{{ route("officina.prenota.modifica", $pren->id) }}"
                                    >
                                        Mod.
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endempty
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
