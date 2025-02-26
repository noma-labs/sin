@extends("layouts.app")

@section("title", "Agraria")

@section("navbar-link")
<li class="nav-item">
    <a class="nav-link" href="{{route('agraria.index')}}">Agraria</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="manDropdown" role="button"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Manutenzione</a>
    <div class="dropdown-menu" aria-labelledby="manDropdown">
        <a class="dropdown-item" href="{{route('manutenzioni.ricerca')}}">Ricerca</a>
        <a class="dropdown-item" href="{{route('manutenzioni.nuova')}}">Inserisci</a>
        <a class="dropdown-item" href="{{route('manutenzioni.programmate')}}">Programmate</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="tratDropdown" role="button"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mezzo Agricolo</a>
    <div class="dropdown-menu" aria-labelledby="tratDropdown">
        <!-- <a class="dropdown-item" href="#">Ricerca</a> -->
        <a class="dropdown-item" href="{{route('mezzo.new')}}">Inserisci</a>
    </div>
</li>
@endsection

@section("content")
    @include("partials.header", ["title" => "Home"])

    <div class="row">
        <div class="col-md-8">
            <div class="card card-mod">
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Prossime Manutenzioni</h3>
                </div>
                <div class="card-body card-body-mod">
                    <a href="{{ route("mezzi.aggiorna.ore") }}">
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
                                        <button
                                            type="button"
                                            href="{{ route("mezzo.show", $mezzo->id) }}"
                                            class="btn btn-secondary"
                                        >
                                            Dettaglio
                                        </button>
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
