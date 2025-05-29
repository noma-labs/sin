@extends("agraria.index")

@section("title", "Agraria")

@section("content")
    @include("partials.header", ["title" => "Agraria"])

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Costo anno ({{ now()->year }})</h3>
                </div>
                <div class="card-body text-center">
                    <span class="fw-bold display-6 text-success">
                        €
                        {{ isset($costCurrentYear) ? number_format($costCurrentYear, 2, ",", ".") : "0,00" }}
                    </span>
                    @if (isset($yoyPerc))
                        <div class="mt-2">
                            @if ($yoyPerc > 0)
                                <span class="fw-bold text-danger">
                                    ▲
                                    {{ number_format($yoyPerc, 1, ",", ".") }}%
                                    rispetto all'anno precedente
                                </span>
                            @elseif ($yoyPerc < 0)
                                <span class="fw-bold text-success">
                                    ▼
                                    {{ number_format(abs($yoyPerc), 1, ",", ".") }}%
                                    rispetto all'anno precedente
                                </span>
                            @else
                                <span class="fw-bold text-muted">
                                    = 0% rispetto all'anno precedente
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Trattori con costo maggiore</h3>
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
    </div>
    <div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
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
                                <th>Lavori Extra</th>
                                <th>Manut.</th>
                                <th>Persona</th>
                                <th>Oper.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($done as $d)
                                <tr>
                                    <td>{{ $d->mezzo->nome }}</td>
                                    <td>{{ $d->data }}</td>
                                    <td>{{ $d->lavori_extra }}</td>
                                    <td>{{ $d->persona }}</td>
                                    <td>
                                        @if ($d->programmate && $d->programmate->count())
                                            <span class="text-lowercase">
                                                {{ $d->programmate->pluck("nome")->implode(", ") }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route("agraria.maintenanace.show", $d->id) }}"
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
