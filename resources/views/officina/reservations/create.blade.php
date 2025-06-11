@extends("officina.index")

@section("title", "Prenotazioni")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Prenotazioni"])

    <form method="POST" action="{{ route("officina.prenota") }}" class="mb-3">
        @csrf

        <livewire:prenotazione-veicoli />

        <div class="row g-3 mb-3">
            <div class="col-md-2">
                <label for="person" class="form-label">Cliente</label>
                <select class="form-select" id="person" name="nome">
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
            <div class="col-md-2">
                <label for="meccanico" class="form-label">Meccanico</label>
                <select
                    class="form-select"
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
            <div class="col-md-2">
                <label class="form-label" for="uso">Uso</label>
                <select class="form-select" id="uso" name="uso" required>
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
            <div class="col-md-2">
                <label class="form-label" for="destinazione">
                    Destinazione
                </label>
                <input
                    type="text"
                    class="form-control"
                    id="destinazione"
                    name="destinazione"
                    value="Grosseto"
                />
            </div>
            <div class="col-md-4">
                <label class="form-label" for="note">Note</label>
                <input
                    type="text"
                    class="form-control"
                    id="note"
                    value="{{ old("note") }}"
                    name="note"
                />
            </div>
        </div>
        <div class="row">
            <div class="col d-flex align-items-end">
                <button
                    type="submit"
                    id="prenota"
                    class="btn btn-primary ms-auto"
                >
                    Prenota
                </button>
            </div>
        </div>
    </form>

    <div class="table-responsive mb-3">
        <table class="table table-hover table-sm">
            <thead class="table-warning">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Stato</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Macchina</th>
                    <th scope="col">Data P./A.</th>
                    <th scope="col">Ora P.</th>
                    <th scope="col">Ora A.</th>
                    <th scope="col">Meccanico</th>
                    <th scope="col">Uso</th>
                    <th scope="col">Destinazione</th>
                    <th scope="col">Note</th>
                    <th scope="col">Oper.</th>
                </tr>
            </thead>
            <tbody class="table-primary">
                @foreach ($prenotazioni as $pren)
                    @empty($pren->delated_at)
                        <tr scope="row">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($pren->isPartita())
                                    <span class="badge bg-danger">Partita</span>
                                @elseif ($pren->deveAncoraPartire())
                                    <span class="badge bg-warning">
                                        Deve Partire
                                    </span>
                                @elseif ($pren->isArrivata())
                                    <span class="badge bg-success">
                                        Arrivata
                                    </span>
                                @endif
                            </td>
                            <td>{{ $pren->cliente->nominativo }}</td>
                            <td>
                                {{ $pren->veicolo->nome }}
                                @if ($pren->veicolo->deleted_at)
                                    <span class="badge bg-danger">
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
                                        <form
                                            method="POST"
                                            action="{{ route("officina.prenota.delete", $pren->id) }}"
                                            style="display: inline"
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
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endempty
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
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
