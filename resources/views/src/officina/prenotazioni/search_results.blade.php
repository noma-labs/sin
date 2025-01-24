@extends("officina.index")

@section("title", "Prenotazioni")

@section("content")
    @include("partials.header", ["title" => "Ricerca Prenotazioni"])

    @include("officina.prenotazioni.search_form")

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        Ricerca effettuata:
        <strong>{{ $msgSearch }}</strong>
        <a
            href="#"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="close"
        >
            &times;
        </a>
    </div>

    <div id="results" class="alert alert-success">
        Numero di prenotazioni trovate:
        <strong>{{ $prenotazioni->total() }}</strong>
    </div>

    <!-- inizio tabella prenotazioni -->
    <div class="table-responsive">
        <table
            class="table table-hover table-bordered"
            style="table-layout: auto; overflow-x: scroll"
        >
            <thead class="thead-inverse">
                <tr>
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
                        <tr hoverable>
                            <td>{{ $pren->cliente->nominativo }}</td>
                            <td>
                                {{ $pren->veicolo()->withTrashed()->first()->nome }}
                                @if ($pren->veicolo()->withTrashed()->first()->deleted_at)
                                    <span class="badge text-bg-danger">
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
                                    <a
                                        role="button"
                                        class="btn btn-danger"
                                        href="{{ route("officina.prenota.delete", $pren->id) }}"
                                    >
                                        Eli.
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
            {{ $prenotazioni->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
        </div>
    </div>
@endsection
