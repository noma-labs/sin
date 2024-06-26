@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Categorie  " . $persona->nominativo])

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Categoria attuale</div>
                <div class="card-body">
                    @if ($categoriaAttuale)
                        <div class="row">
                            <p class="col-md-3 font-weight-bold">Categoria</p>
                            <p class="col-md-3 font-weight-bold">Data Inizio</p>
                            <p class="col-md-3 font-weight-bold">
                                Tempo trascorso
                            </p>
                            <p class="col-md-3 font-weight-bold">Operazioni</p>
                        </div>
                        <div class="row">
                            <p class="col-md-3">
                                {{ $categoriaAttuale->nome }}
                            </p>
                            <p class="col-md-3">
                                {{ $categoriaAttuale->pivot->data_inizio }}
                            </p>
                            <div class="col-md-3">
                                <span class="badge badge-info">
                                    @diffHumans($categoriaAttuale->pivot->data_inizio)
                                </span>
                            </div>
                            <div class="col-md-3">
                                <my-modal
                                    modal-title="Modifica Categoria attuale"
                                    button-title="Modifica"
                                    button-style="btn-warning my-2"
                                >
                                    <template slot="modal-body-slot">
                                        <form
                                            class="form"
                                            method="POST"
                                            id="formPersonaCategoriaModifica"
                                            action="{{ route("nomadelfia.persone.categoria.modifica", ["idPersona" => $persona->id, "id" => $categoriaAttuale->id]) }}"
                                        >
                                            {{ csrf_field() }}
                                            <div class="form-group row">
                                                <label
                                                    for="staticEmail"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Categoria attuale
                                                </label>
                                                <div class="col-sm-6">
                                                    <div>
                                                        {{ $categoriaAttuale->nome }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Data inizio
                                                </label>
                                                <div class="col-sm-6">
                                                    <date-picker
                                                        :bootstrap-styling="true"
                                                        value="{{ $categoriaAttuale->pivot->data_inizio }}"
                                                        format="yyyy-MM-dd"
                                                        name="data_inizio"
                                                    ></date-picker>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    for="inputPassword"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Data fine categoria
                                                </label>
                                                <div class="col-sm-6">
                                                    <date-picker
                                                        :bootstrap-styling="true"
                                                        value="{{ $categoriaAttuale->pivot->data_fine }}"
                                                        format="yyyy-MM-dd"
                                                        name="data_fine"
                                                    ></date-picker>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label
                                                    for="inputPassword"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Stato
                                                </label>
                                                <div class="col-sm-6">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="stato"
                                                            id="forstatoM"
                                                            value="1"
                                                            @if($categoriaAttuale->pivot->stato=='1') checked @endif
                                                        />
                                                        <label
                                                            class="form-check-label"
                                                            for="forstatoM"
                                                        >
                                                            Attiva
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="stato"
                                                            id="forstatoF"
                                                            value="0"
                                                            @if($categoriaAttuale->pivot->stato=='0') checked @endif
                                                        />
                                                        <label
                                                            class="form-check-label"
                                                            for="forstao"
                                                        >
                                                            Disattiva
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </template>
                                    <template slot="modal-button">
                                        <button
                                            class="btn btn-success"
                                            form="formPersonaCategoriaModifica"
                                        >
                                            Salva
                                        </button>
                                    </template>
                                </my-modal>
                                <!--end modal modifica categoria-->

                                @include("nomadelfia.templates.eliminaPersonaCategoria", ["persona" => $persona, "categoria" => $categoriaAttuale])
                            </div>
                        </div>
                    @else
                        <p class="text-danger">Nessuna categoria</p>
                    @endif
                    <my-modal
                        modal-title="Aggiungi Categoria persona"
                        button-title="Nuova Categoria"
                        button-style="btn-success  my-2"
                    >
                        <template slot="modal-body-slot">
                            <form
                                class="form"
                                method="POST"
                                id="formPersonaCategoria"
                                action="{{ route("nomadelfia.persone.categoria.assegna", ["idPersona" => $persona->id]) }}"
                            >
                                {{ csrf_field() }}
                                @if ($categoriaAttuale)
                                    <h5 class="my-2">
                                        Completa dati della categoria attuale:
                                        {{ $categoriaAttuale->nome }}
                                    </h5>
                                    <div class="form-group row">
                                        <label
                                            for="inputPassword"
                                            class="col-sm-6 col-form-label"
                                        >
                                            Data fine categoria
                                        </label>
                                        <div class="col-sm-6">
                                            <date-picker
                                                :bootstrap-styling="true"
                                                value="{{ Carbon::now()->toDateString() }}"
                                                format="yyyy-MM-dd"
                                                name="data_fine"
                                            ></date-picker>
                                            <small
                                                id="emailHelp"
                                                class="form-text text-muted"
                                            >
                                                Lasciare vuoto se concide con la
                                                data di inizio della nuova
                                                categoria .
                                            </small>
                                        </div>
                                    </div>
                                    <hr />
                                @endif

                                <h5 class="my-2">
                                    Inserimento nuova categoria
                                </h5>
                                <div class="form-group row">
                                    <label
                                        for="staticEmail"
                                        class="col-sm-6 col-form-label"
                                    >
                                        Categoria
                                    </label>
                                    <div class="col-sm-6">
                                        <select
                                            name="categoria_id"
                                            class="form-control"
                                        >
                                            <option value="" selected>
                                                ---seleziona categoria---
                                            </option>
                                            @foreach ($persona->categoriePossibili() as $categoria)
                                                <option
                                                    value="{{ $categoria->id }}"
                                                >
                                                    {{ $categoria->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">
                                        Data inizio
                                    </label>
                                    <div class="col-sm-6">
                                        <!-- <input type="date" name="data_inizio" class="form-control" id="inputPassword" placeholder="Password"> -->
                                        <date-picker
                                            :bootstrap-styling="true"
                                            value="{{ old("data_inizio") ? old("data_inizio") : Carbon::now()->toDateString() }}"
                                            format="yyyy-MM-dd"
                                            name="data_inizio"
                                        ></date-picker>
                                    </div>
                                </div>
                            </form>
                        </template>
                        <template slot="modal-button">
                            <button
                                class="btn btn-success"
                                form="formPersonaCategoria"
                            >
                                Salva
                            </button>
                        </template>
                    </my-modal>
                    <!--end modal aggiungi categoria-->
                </div>
                <!--end card body-->
            </div>
            <!--end card -->
            <div class="card my-3">
                <div class="card-header">Storico delle Categoria</div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 font-weight-bold">Categoria</p>
                        <p class="col-md-3 font-weight-bold">Data inizio</p>
                        <p class="col-md-3 font-weight-bold">Data fine</p>
                        <p class="col-md-3 font-weight-bold">Durata</p>
                    </div>

                    @forelse ($persona->categorieStorico as $categoriastorico)
                        <div class="row">
                            <p class="col-md-3">
                                {{ $categoriastorico->nome }}
                            </p>
                            <p class="col-md-3">
                                {{ $categoriastorico->pivot->data_inizio }}
                            </p>
                            <p class="col-md-3">
                                {{ $categoriastorico->pivot->data_fine }}
                            </p>

                            <div class="col-md-3">
                                <span class="badge badge-info">
                                    {{ Carbon::parse($categoriastorico->pivot->data_fine)->diffForHumans(Carbon::parse($categoriastorico->pivot->data_inizio), ["short" => true]) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-danger">
                            Nessuna categoria nello storico
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
