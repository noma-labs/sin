@extends("agraria.index")

@section("title", "Agraria")

@section("content")
    @include("partials.header", ["title" => "Home"])

    <div class="row">
        <div class="col-md-8">
            <div class="card card-mod">
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Prossime Manutenzioni</h3>
                </div>
                <div class="card-body card-body-mod">
                    <a href="{{ route("agraria.vehicle.hour.create") }}">
                        Aggiorna ore veicoli
                    </a>
                    <table class="table table-hover table-bordered">
                        <thead class="thead-inverse table-sm">
                            <tr>
                                <th>Nome Mezzo</th>
                                <th>Tipo Manutenzione</th>
                                <th>Scadenza Ore</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prossime as $p)
                                <tr>
                                    <td>{{ $p["nome"] }}</td>
                                    <td>{{ $p["manutenzione"] }}</td>
                                    <td>
                                        @if ($p["ore"] < 0)
                                            <span class="badge bg-danger">
                                                {{ "scaduta da: " . abs($p["ore"]) . " ore" }}
                                            </span>
                                        @elseif ($p["ore"] < 50)
                                            <span class="badge bg-warning">
                                                {{ "scade tra: " . abs($p["ore"]) . " ore" }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                {{ "scade tra: " . abs($p["ore"]) . " ore" }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card card-mod">
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Ultime Manutenzioni</h3>
                </div>
                <div class="card-body card-body-mod">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-inverse">
                            <tr>
                                <th width="15%">Data</th>
                                <th width="20%">Nome Mezzo</th>
                                <th width="55%">Lavori Fatti</th>
                                <th width="10%">Persona</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ultime as $u)
                                <tr>
                                    <td>{{ $u["data"] }}</td>
                                    <td>{{ $u["mezzo"] }}</td>
                                    <td>{{ $u["lavori"] }}</td>
                                    <td>{{ $u["persona"] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-mod">
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Stato Mezzi</h3>
                </div>
                <div class="card-body card-body-mod">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Stato</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mezzi as $mezzo)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mezzo->nome }}</td>
                                    <td>
                                        <a
                                            href="{{ route("agraria.vehicle.show", $mezzo->id) }}"
                                            class="btn btn-secondary"
                                        >
                                            Dettaglio
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
