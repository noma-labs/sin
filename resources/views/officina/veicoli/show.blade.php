@extends("officina.index")

@section("archivio")
    @include("partials.header", ["title" => "Dettaglio Veicolo"])
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Targa</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $veicolo->targa }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $veicolo->nome }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $veicolo->modello->marca->nome }}"
                            disabled
                        />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="modello">Modello</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $veicolo->modello->nome }}"
                            disabled
                        />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipologia">Tipologia</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $veicolo->tipologia->nome }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="impiego">Impiego</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $veicolo->impiego->nome }}"
                            disabled
                        />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="alimentazione">Alimentazione</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $veicolo->alimentazione->nome }}"
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="posti">N. Posti</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $veicolo->num_posti }}"
                            disabled
                        />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipologia">Filtro Olio</label>
                        <input
                            type="text"
                            class="form-control"
                            @if($veicolo->filtroOlio) value="{{ $veicolo->filtroOlio->codice }}" @endif
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="impiego">Filtro Gasolio</label>
                        <input
                            type="text"
                            class="form-control"
                            @if($veicolo->filtroGasolio) value="{{ $veicolo->filtroGasolio->codice }}" @endif
                            disabled
                        />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="alimentazione">Filtro Aria</label>
                        <input
                            type="text"
                            class="form-control"
                            @if($veicolo->filtroAria) value="{{ $veicolo->filtroAria->codice }}" @endif
                            disabled
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="posti">Filtro A.C.</label>
                        <input
                            type="text"
                            class="form-control"
                            @if($veicolo->filtroAriaCondizionata) value="{{ $veicolo->filtroAriaCondizionata->codice }}" @endif
                            disabled
                        />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="olio">Tipo Di Olio</label>
                        <input
                            type="text"
                            class="form-control"
                            @if($veicolo->olioMotore) value="{{ $veicolo->olioMotore->codice }}" @endif
                            disabled
                        />
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="litri_olio">Litri Olio</label>
                        <input
                            type="number"
                            class="form-control"
                            @if($veicolo->litri_olio) value="{{ $veicolo->litri_olio }}" @endif
                            disabled
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-mod">
                <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Tipi Di Gomme</h3>
                </div>
                <div class="card-body card-body-mod">
                    <ul class="list-group list-group-flush">
                        @foreach ($veicolo->gomme as $g)
                            <li class="list-group-item">
                                {{ $g->codice }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card card-mod">
                <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Documenti</h3>
                </div>
                <div class="card-body card-body-mod">
                    <ul class="list-group list-group-flush"></ul>
                </div>
            </div>
        </div>
    </div>

    <a
        class="btn btn-primary"
        href="{{ route("veicoli.modifica", ["id" => $veicolo->id]) }}"
    >
        Modifica
    </a>

    <!-- end section dettagli prenotazione -->
@endsection
