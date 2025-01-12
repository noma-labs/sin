@extends("officina.index")

@section("content")
    @include("partials.header", ["title" => "Modifica Veicolo"])

    <div class="row">
        <div class="col-md-9">
            <form
                method="POST"
                id="veicolo-form-modifica"
                action="{{ route("veicoli.modifica.confirm", $veicolo->id) }}"
            >
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Targa</label>
                            <input
                                type="text"
                                class="form-control"
                                name="targa"
                                value="{{ $veicolo->targa }}"
                            />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input
                                type="text"
                                class="form-control"
                                name="nome"
                                value="{{ $veicolo->nome }}"
                            />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="marca">Marca</label>
                            <select
                                class="form-control"
                                name="marca_id"
                                type="text"
                            >
                                @foreach ($marche as $marca)
                                    <option
                                        value="{{ $marca->id }}"
                                        @if($marca->id == $veicolo->modello->marca->id) selected @endif
                                    >
                                        {{ $marca->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="modello_id">Modello</label>
                            <select
                                class="form-control"
                                name="modello_id"
                                type="text"
                            >
                                @foreach ($modelli as $mod)
                                    <option
                                        value="{{ $mod->id }}"
                                        @if($mod->id == $veicolo->modello->id) selected @endif
                                    >
                                        {{ $mod->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipologia">Tipologia</label>
                            <select
                                class="form-control"
                                name="tipologia_id"
                                type="text"
                            >
                                @foreach ($tipologie as $tipologia)
                                    <option
                                        value="{{ $tipologia->id }}"
                                        @if($tipologia->id == $veicolo->tipologia_id) selected @endif
                                    >
                                        {{ $tipologia->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="impiego">Impiego</label>
                            <select
                                class="form-control"
                                name="impiego_id"
                                type="text"
                            >
                                @foreach ($impieghi as $impiego)
                                    <option
                                        value="{{ $impiego->id }}"
                                        @if($impiego->id == $veicolo->impiego_id) selected @endif
                                    >
                                        {{ $impiego->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="alimentazione">Alimentazione</label>
                            <select
                                class="form-control"
                                name="alimentazione_id"
                                type="text"
                            >
                                @foreach ($alimentazioni as $alimentazione)
                                    <option
                                        value="{{ $alimentazione->id }}"
                                        @if($alimentazione->id == $veicolo->alimentazione_id) selected @endif
                                    >
                                        {{ $alimentazione->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="posti">N. Posti</label>
                            <input
                                type="text"
                                class="form-control"
                                name="num_posti"
                                value="{{ $veicolo->num_posti }}"
                            />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipologia">Filtro Olio</label>
                            <select
                                class="form-control"
                                name="filtro_olio"
                                type="text"
                            >
                                <option value="" hidden selected>
                                    --Seleziona--
                                </option>
                                @foreach ($f_olio as $filtro)
                                    <option
                                        value="{{ $filtro->id }}"
                                        @if($filtro->id == $veicolo->filtro_olio) selected @endif
                                    >
                                        {{ $filtro->codice }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipologia">Filtro Gasolio</label>
                            <select
                                class="form-control"
                                name="filtro_gasolio"
                                type="text"
                            >
                                <option value="" hidden selected>
                                    --Seleziona--
                                </option>
                                @foreach ($f_gasolio as $filtro)
                                    <option
                                        value="{{ $filtro->id }}"
                                        @if($filtro->id == $veicolo->filtro_gasolio) selected @endif
                                    >
                                        {{ $filtro->codice }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipologia">Filtro Aria</label>
                            <select
                                class="form-control"
                                name="filtro_aria"
                                type="text"
                            >
                                <option value="" hidden selected>
                                    --Seleziona--
                                </option>
                                @foreach ($f_aria as $filtro)
                                    <option
                                        value="{{ $filtro->id }}"
                                        @if($filtro->id == $veicolo->filtro_aria) selected @endif
                                    >
                                        {{ $filtro->codice }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipologia">Filtro A.C.</label>
                            <select
                                class="form-control"
                                name="filtro_aria_condizionata"
                                type="text"
                            >
                                <option value="" hidden selected>
                                    --Seleziona--
                                </option>
                                @foreach ($f_ac as $filtro)
                                    <option
                                        value="{{ $filtro->id }}"
                                        @if($filtro->id == $veicolo->filtro_aria_condizionata) selected @endif
                                    >
                                        {{ $filtro->codice }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="olio_id">Tipo Di Olio</label>
                            <select
                                class="form-control"
                                name="olio_id"
                                type="text"
                            >
                                <option value="" hidden selected>
                                    --Seleziona--
                                </option>
                                @foreach ($olio_motore as $o)
                                    <option
                                        value="{{ $o->id }}"
                                        @if($o->id == $veicolo->olio_id) selected @endif
                                    >
                                        {{ $o->codice }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="litri_olio">Litri Olio</label>
                            <input
                                type="number"
                                name="litri_olio"
                                class="form-control"
                                @if($veicolo->litri_olio) value="{{ $veicolo->litri_olio }}" @endif
                            />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input
                                    class="form-check-input"
                                    type="hidden"
                                    name="prenotabile"
                                    value="0"
                                />
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="prenotabile"
                                    value="1"
                                    @if($veicolo->prenotabile) checked=checked @endif
                                />
                                Il veicolo è prenotabile
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-3">
            <div class="card card-mod">
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Tipi Di Gomme</h3>
                </div>
                <div class="card-body card-body-mod">
                    <ul class="list-group list-group-flush">
                        @foreach ($veicolo->gomme()->get() as $gv)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-sm-8">
                                        {{ $gv->codice }}
                                    </div>
                                    <div class="col-sm-4">
                                        <form
                                            action="{{ route("veicoli.gomme.delete", ["id" => $veicolo->id, "idGomma" => $gv->id]) }}"
                                            id="form-delete-{{ $gv->id }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method("DELETE")
                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm"
                                            >
                                                Elim.
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    @include("officina.veicoli.assegnaGomma")
                </div>
            </div>

            <div class="card card-mod">
                <div class="card-header card-header-mod">
                    <h3 class="card-title">Documenti</h3>
                </div>
                <div class="card-body card-body-mod"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            @include("officina.veicoli.aggiungiFiltro")
        </div>
        <div class="col-md-2">
            <my-modal
                modal-title="Aggiungi Tipo Olio"
                button-title="Aggiungi Olio"
                button-style="btn-block btn-warning"
            >
                <template v-slot:modal-body-slot>
                    <form
                        method="POST"
                        action="{{ route("olio.aggiungi") }}"
                        id="form-aggiungi-olio"
                    >
                        {{ csrf_field() }}
                        <input
                            type="hidden"
                            name="veicolo"
                            value="{{ $veicolo->id }}"
                        />
                        <div class="row">
                            <div class="col-md-6">
                                <label for="codice">Codice Olio</label>
                                <input
                                    name="codice"
                                    type="text"
                                    id="codice"
                                    class="form-control"
                                    placeholder="es. 15W/40"
                                />
                            </div>
                            <div class="col-md-6">
                                <label for="note">Note</label>
                                <input
                                    type="text"
                                    name="note"
                                    id="note"
                                    class="form-control"
                                    value=""
                                    placeholder="Nota Facoltativa..."
                                />
                            </div>
                        </div>
                    </form>
                </template>
                <template v-slot:modal-button>
                    <button
                        class="btn btn-success"
                        type="submit"
                        form="form-aggiungi-olio"
                    >
                        Salva
                    </button>
                </template>
            </my-modal>
        </div>
        <div class="col-md-2">
            <my-modal
                modal-title="Aggiungi Gomma"
                button-title="Aggiungi Gomma"
                button-style="btn-block btn-warning"
            >
                <template v-slot:modal-body-slot>
                    <form
                        method="POST"
                        action="{{ route("gomma.aggiungi") }}"
                        id="form-aggiungi-gomma"
                    >
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <label for="codice">Codice Gomma</label>
                                <input
                                    name="codice"
                                    type="text"
                                    id="codice"
                                    class="form-control"
                                    placeholder="es. 170/75 R17"
                                />
                            </div>
                        </div>
                    </form>
                </template>
                <template v-slot:modal-button>
                    <button
                        class="btn btn-success"
                        type="submit"
                        form="form-aggiungi-gomma"
                    >
                        Salva
                    </button>
                </template>
            </my-modal>
        </div>
        @if ($veicolo->deleted_at)
            <div class="col-md-2 offset-md-2">
                <my-modal
                    modal-title="Elimina Veicolo Definitivamente"
                    button-title="Elimina Definitivamente"
                    button-style="btn-danger btn-block"
                >
                    <template v-slot:modal-body-slot>
                        <div class="alert alert-danger" role="alert">
                            <p>
                                Attenzione si sta per eliminare il veicolo:
                                <strong>{{ $veicolo->nome }}</strong>
                                .
                            </p>
                            <p>
                                Il veicolo verrà eliminato
                                <span class="font-italic">definitivamente</span>
                                e non sarà più possibile recuperarlo.
                            </p>
                            <form
                                action="{{ route("veicoli.elimina.definitivamente") }}"
                                method="post"
                                id="form-elimina"
                            >
                                @csrf
                                @method("DELETE")
                                <input
                                    name="v_id"
                                    type="hidden"
                                    value="{{ $veicolo->id }}"
                                />
                            </form>
                        </div>
                    </template>
                    <template v-slot:modal-button>
                        <button
                            type="submit"
                            class="btn btn-success"
                            form="form-elimina"
                        >
                            Ok
                        </button>
                    </template>
                </my-modal>
            </div>
            <div class="col-md-2">
                <my-modal
                    modal-title="Riabilita Veicolo"
                    button-title="Riabilita Veicolo"
                    button-style="btn-danger btn-block"
                >
                    <template v-slot:modal-body-slot>
                        <div class="alert alert-warning" role="alert">
                            <p>
                                Attenzione si sta per riabilitare il veicolo:
                                <strong>{{ $veicolo->nome }}</strong>
                                .
                            </p>
                            <p>
                                Il veicolo tornerà disponibile nei veicoli della
                                comunità.
                            </p>
                            <form
                                action="{{ route("veicolo.riabilita") }}"
                                method="post"
                                id="form-riabilita"
                            >
                                @csrf
                                @method("POST")
                                <input
                                    name="v_id"
                                    type="hidden"
                                    value="{{ $veicolo->id }}"
                                />
                            </form>
                        </div>
                    </template>
                    <template v-slot:modal-button>
                        <button
                            type="submit"
                            class="btn btn-success"
                            form="form-riabilita"
                        >
                            Ok
                        </button>
                    </template>
                </my-modal>
            </div>
        @else
            <div class="col-md-2 offset-md-2">
                <my-modal
                    modal-title="Demolisci Veicolo"
                    button-title="Demolisci"
                    button-style="btn-danger btn-block"
                >
                    <template v-slot:modal-body-slot>
                        <div class="alert alert-danger" role="alert">
                            <p>
                                Attenzione si sta per demolire il veicolo:
                                <strong>{{ $veicolo->nome }}</strong>
                                .
                            </p>
                            <p>
                                Il veicolo verrà comunque conservato come
                                <span class="font-italic">
                                    veicolo demolito
                                </span>
                                .
                            </p>
                            <form
                                action="{{ route("veicoli.demolisci") }}"
                                method="post"
                                id="form-demolisci"
                            >
                                @csrf
                                @method("DELETE")
                                <input
                                    name="v_id"
                                    type="hidden"
                                    value="{{ $veicolo->id }}"
                                />
                            </form>
                        </div>
                    </template>
                    <template v-slot:modal-button>
                        <button
                            type="submit"
                            class="btn btn-success"
                            form="form-demolisci"
                        >
                            Ok
                        </button>
                    </template>
                </my-modal>
            </div>
        @endif
        <div class="col-md-2">
            <button
                class="btn btn-block btn-success"
                form="veicolo-form-modifica"
                type="submit"
            >
                Salva
            </button>
        </div>
    </div>
@endsection
