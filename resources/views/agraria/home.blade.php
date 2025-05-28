@extends("agraria.index")

@section("title", "Agraria")

@section("content")
    @include("partials.header", ["title" => "Agraria"])

    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Costo manutezioni</h3>
                </div>
                <div class="card-body">
                    @if (isset($mezziCostosi) && count($mezziCostosi))
                        <ul class="list-group list-group-flush">
                            @foreach ($mezziCostosi as $mezzo)
                                <li
                                    class="list-group-item py-1 px-2 d-flex justify-content-between align-items-center"
                                >
                                    <a
                                        href="{{ route("agraria.vehicle.show", $mezzo->id) }}"
                                    >
                                        {{ $mezzo->nome }}
                                    </a>
                                    <span></span>
                                    <span class="fw-bold text-danger">
                                        €
                                        {{ number_format($mezzo->totale_spesa, 2, ",", ".") }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">Nessun dato disponibile.</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Prossime Manutenzioni</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-inverse table-sm">
                                <tr>
                                    <th>Mezzo</th>
                                    <th>Manutenzione</th>
                                    <th>Scadenza Ore</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prossime as $p)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ route("agraria.vehicle.show", $p["id"]) }}"
                                            >
                                                {{ $p["nome"] }}
                                            </a>
                                        </td>
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
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ultime Manutenzioni</h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-inverse">
                            <tr>
                                <th>Nome Mezzo</th>
                                <th>Data</th>
                                <th>Lavori Fatti</th>
                                <th>Persona</th>
                                <th>Oper.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ultime as $u)
                                <tr>
                                    <td>{{ $u["mezzo"] }}</td>
                                    <td>{{ $u["data"] }}</td>
                                    <td>{{ $u["lavori"] }}</td>
                                    <td>{{ $u["persona"] }}</td>
                                    <td>
                                        <a
                                            href="{{ route("agraria.maintenanace.show", $u["id"]) }}"
                                            class="btn btn-sm btn-secondary"
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
