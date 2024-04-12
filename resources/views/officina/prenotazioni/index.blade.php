@extends("officina.index")

@section("title", "Prenotazioni")

@section("archivio")
    @include("partials.header", ["title" => "Aggiungi Prenotazioni"])

    <form
        class="container-fluid"
        id="needs-validation"
        method="POST"
        action="{{ route("officina.prenota") }}"
    >
        {{ csrf_field() }}

        <veicolo-prenotazione
            data-partenza="{{ old("data_par") != "" ? old("data_par") : Carbon::now()->toDateString() }}"
            data-arrivo="{{ old("data_arr") != "" ? old("data_arr") : Carbon::now()->toDateString() }}"
            ora-partenza="{{ old("ora_par") }}"
            ora-arrivo="{{ old("ora_arr") }}"
            url-veicoli-prenotazioni="{{ route("api.officina.veicoli.prenotazioni") }}"
        ></veicolo-prenotazione>

        <livewire:prenotazione-veicoli />

        <div class="row">
            <div class="col-md-3">
                <label class="control-label">Cliente</label>
                <select class="form-control" id="cliente" name="nome">
                    <option selected value>--Seleziona--</option>
                    @foreach ($clienti as $cliente)
                        <option
                            value="{{ $cliente->id }}"
                            @if (old('nome') !== null && old('nome') == $cliente->id)  selected @endif
                        >
                            {{ $cliente->nominativo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="meccanico">Meccanico</label>
                    <select
                        class="form-control"
                        id="meccanico"
                        name="meccanico"
                        required
                    >
                        <option selected disabled>--Seleziona--</option>
                        @foreach ($meccanici as $mecc)
                            <option
                                value="{{ $mecc->persona_id }}"
                                @if (old('meccanico') === (string)$mecc->persona_id) selected @endif
                                @if (strtolower($mecc->nominativo) == 'gennaro' OR strtolower($mecc->nominativo) == 'carlo s.') disabled @endif
                            >
                                {{ $mecc->nominativo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="uso">Uso</label>
                    <select class="form-control" id="uso" name="uso" required>
                        <option disabled selected>--Seleziona--</option>
                        @foreach ($usi as $uso)
                            <option
                                value="{{ $uso->ofus_iden }}"
                                @if (old('uso') == $uso->ofus_iden) selected @endif
                            >
                                {{ $uso->ofus_nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="destinazione">Destinazione</label>
                    <input
                        type="text"
                        class="form-control"
                        id="destinazione"
                        name="destinazione"
                        value="Grosseto"
                    />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    <label for="note">Note</label>
                    <input
                        type="text"
                        class="form-control"
                        id="note"
                        value="{{ old("note") }}"
                        name="note"
                    />
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="prenota"><br /></label>
                    <button
                        type="submit"
                        id="prenota"
                        class="btn btn-block btn-primary"
                    >
                        Prenota
                    </button>
                </div>
            </div>
        </div>
        <br />
    </form>

    <!-- inizio tabella prenotazioni -->
    <div class="table-responsive">
        <table
            class="table table-hover table-bordered table-sm"
            style="table-layout: auto; overflow-x: scroll"
        >
            <thead class="thead-inverse">
                <tr>
                    <th width="2%">#</th>
                    <th width="5%">Stato</th>
                    <th width="7%">Nome</th>
                    <th width="12%">Macchina</th>
                    <th width="10%">Data P./A.</th>
                    <th width="3%">Ora P.</th>
                    <th width="3%">Ora A.</th>
                    <th width="10%">Meccanico</th>
                    <th width="8%">Uso</th>
                    <th width="10%">Destinazione</th>
                    <th width="15%">Note</th>
                    <th width="15%">Oper.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prenotazioni as $pren)
                    @empty($pren->delated_at)
                        <tr hoverable>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($pren->isPartita())
                                    <span class="badge badge-danger">
                                        Partita
                                    </span>
                                @elseif ($pren->deveAncoraPartire())
                                    <span class="badge badge-warning">
                                        Deve Partire
                                    </span>
                                @elseif ($pren->isArrivata())
                                    <span class="badge badge-success">
                                        Arrivata
                                    </span>
                                @endif
                            </td>
                            <td>{{ $pren->cliente->nominativo }}</td>
                            <td>
                                {{ $pren->veicolo->nome }}
                                @if ($pren->veicolo->deleted_at)
                                    <span class="badge badge-danger">
                                        demolito
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($pren->data_partenza == $pren->data_arrivo)
                                    {{ $pren->data_partenza }}
                                @else
                                    {{ $pren->data_partenza }}
                                    <br />
                                    {{ $pren->data_arrivo }}
                                @endif
                            </td>
                            <td>{{ $pren->ora_partenza }}</td>
                            <td>{{ $pren->ora_arrivo }}</td>
                            <td>{{ $pren->meccanico->nominativo }}</td>
                            <td>{{ $pren->uso->ofus_nome }}</td>
                            <td>{{ $pren->destinazione }}</td>
                            <td>{{ $pren->note }}</td>
                            <td>
                                <div
                                    class="btn-group"
                                    role="group"
                                    aria-label="Basic example"
                                >
                                    @can("meccanica.prenotazione.modifica")
                                        <a
                                            class="btn btn-warning btn-sm"
                                            href="{{ route("officina.prenota.modifica", $pren->id) }}"
                                        >
                                            Modifica
                                        </a>
                                    @endcan

                                    @can("meccanica.prenotazione.elimina")
                                        <a
                                            class="btn btn-danger btn-sm"
                                            href="{{ route("officina.prenota.delete", $pren->id) }}"
                                        >
                                            Elimina
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endempty
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row justify-content-center mb-2">
        <div class="btn-group btn-group-lg" role="group">
            <a
                role="button"
                href="{{ route("officina.index", ["day" => "ieri"]) }}"
                class="btn btn-danger @if($day == 'ieri') active @endif"
            >
                Ieri
            </a>
            <a
                role="button"
                href="{{ route("officina.index", ["day" => "oggi"]) }}"
                class="btn btn-success @if($day == 'oggi') active @endif"
            >
                Oggi
            </a>
            <a
                role="button"
                href="{{ route("officina.index", ["day" => "all"]) }}"
                class="btn btn-warning @if($day == 'all') active @endif"
            >
                Tutte
            </a>
        </div>
    </div>
@endsection
