@extends("agraria.index")

@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => "Mezzo - " . $mezzo->nome])
    <div class="row">
        <div class="col-md-8">
            <div class="mx-auto" style="width: 250px">
                <foto-mezzo></foto-mezzo>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $mezzo->nome }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nome">Numero di Telaio:</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $mezzo->numero_telaio }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nome">Ore Totali:</label>
                        <input
                            type="number"
                            class="form-control"
                            value="{{ $mezzo->tot_ore }}"
                            disabled
                        />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nome">Gomme Anteriori:</label>
                        <input
                            type="text"
                            class="form-control"
                            @if ($mezzo->gomme_ant)
                                value="{{ $mezzo->gommeAnt->nome }}"
                            @endif
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nome">Gomme Posteriori:</label>
                        <input
                            type="text"
                            class="form-control"
                            @if ($mezzo->gomme_post)
                                value="{{ $mezzo->gommePos->nome }}"
                            @endif
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nome">Filtro Aria Interno:</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $mezzo->filtro_aria_int }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nome">Filtro Aria Esterno:</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $mezzo->filtro_aria_ext }}"
                            disabled
                        />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nome">Filtro Olio:</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $mezzo->filtro_olio }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nome">Filtro Gasolio:</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $mezzo->filtro_gasolio }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nome">Filtro Serviz:</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $mezzo->filtro_servizi }}"
                            disabled
                        />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 offset-md-8">
                    <a
                        href="{{ route("agraria.maintenanace.search.show") . "?id=" . $mezzo->id }}"
                        class="btn btn-warning btn-block"
                    >
                        Manutenzioni
                    </a>
                </div>
                <div class="col-md-2">
                    <a
                        href="{{ route("agraria.vehicle.edit", ["id" => $mezzo->id]) }}"
                        class="btn btn-warning btn-block"
                    >
                        Modifica
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-mod">
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Prossime Manutenzioni</h3>
                </div>
                <div class="card-body card-body-mod">
                    <table class="table table-hover table-bordered">
                        <thead class="table table-sm">
                            <tr>
                                <th scope="col" width="65%">Nome</th>
                                <th scope="col" width="45%">Ore Mancanti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prossime as $k => $v)
                                <tr>
                                    <td>{{ $k }}</td>

                                    @if ($v < 0)
                                        <td>
                                            <span class="badge bg-danger">
                                                {{ "scaduto da: " . abs($v) }}
                                            </span>
                                        </td>
                                    @elseif ($v < 50)
                                        <td>
                                            <span class="badge bg-warning">
                                                {{ "scade tra: " . abs($v) }}
                                            </span>
                                        </td>
                                    @else
                                        <td>
                                            <span class="badge bg-success">
                                                {{ "scade tra: " . abs($v) }}
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
