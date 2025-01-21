@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Aziende " . $persona->nominativo])

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Azienda attuale</div>
                <div class="card-body">
                    @if ($persona->aziendeAttuali()->count() > 0)
                        <div class="row">
                            <p class="col-md-3 fw-bold">Azienda</p>
                            <p class="col-md-2 fw-bold">Data entrata</p>
                            <p class="col-md-3 fw-bold">Mansione</p>
                            <p class="col-md-2 fw-bold">Tempo trascorso</p>
                            <p class="col-md-2 fw-bold">Op.</p>
                        </div>
                    @endif

                    @forelse ($persona->aziendeAttuali as $azienda)
                        <div class="row">
                            <p class="col-md-3">
                                @include("nomadelfia.templates.azienda", ["azienda" => $azienda])
                            </p>
                            <p class="col-md-2">
                                {{ $azienda->pivot->data_inizio_azienda }}
                            </p>
                            <p class="col-md-3">
                                {{ $azienda->pivot->mansione }}
                            </p>
                            <div class="col-md-2">
                                <span class="badge text-bg-info">
                                    @diffHumans($azienda->pivot->data_inizio_azienda)
                                </span>
                            </div>
                            <div class="col-md-2">
                                <x-modal
                                    modal-title="Modifica Azienda attuale"
                                    button-title="Modifica"
                                    button-style="btn-warning my-2"
                                >
                                    <x-slot:body>
                                        <form
                                            class="form"
                                            method="POST"
                                            id="formPersonaGruppoModifica"
                                            action="{{ route("nomadelfia.persone.aziende.modifica", ["idPersona" => $persona->id, "id" => $azienda->id]) }}"
                                        >
                                            {{ csrf_field() }}
                                            <div class="mb-3 row">
                                                <label
                                                    for="staticEmail"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Azienda attuale
                                                </label>
                                                <div class="col-sm-6">
                                                    <div>
                                                        {{ $azienda->nome_azienda }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Data entrata
                                                </label>
                                                <div class="col-sm-6">
                                                    <input
                                                        class="form-control"
                                                        type="date"
                                                        name="data_entrata"
                                                        value="{{ $azienda->pivot->data_inizio_azienda }}"
                                                    />
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label
                                                    for="inputPassword"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Data fine
                                                </label>
                                                <div class="col-sm-6">
                                                    <input
                                                        class="form-control"
                                                        type="date"
                                                        name="data_uscita"
                                                        value="{{ $azienda->pivot->data_fine_azienda }}"
                                                    />
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Mansione
                                                </label>
                                                <div class="col-sm-6">
                                                    <select
                                                        name="mansione"
                                                        class="form-select"
                                                    >
                                                        <option selected>
                                                            ---seleziona
                                                            mansione---
                                                        </option>
                                                        <option
                                                            value="LAVORATORE"
                                                            {{ $azienda->pivot->mansione == "LAVORATORE" ? "selected" : "" }}
                                                        >
                                                            Lavoratore
                                                        </option>
                                                        <option
                                                            value="RESPONSABILE AZIENDA"
                                                            {{ $azienda->pivot->mansione == "RESPONSABILE AZIENDA" ? "selected" : "" }}
                                                        >
                                                            Responsabile azienda
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
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
                                                            value="Attivo"
                                                            @if($azienda->pivot->stato=='Attivo') checked @endif
                                                        />
                                                        <label
                                                            class="form-check-label"
                                                            for="forstatoM"
                                                        >
                                                            Attivo
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="stato"
                                                            id="forstatoM"
                                                            value="Sospeso"
                                                            @if($azienda->pivot->stato=='Sospeso') checked @endif
                                                        />
                                                        <label
                                                            class="form-check-label"
                                                            for="forstatoM"
                                                        >
                                                            Attivo
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="stato"
                                                            id="forstatoF"
                                                            value="Non Attivo"
                                                            @if($azienda->pivot->stato=='Non Attivo') checked @endif
                                                        />
                                                        <label
                                                            class="form-check-label"
                                                            for="forstao"
                                                        >
                                                            Disattivo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </x-slot>
                                    <x-slot:footer>
                                        <button
                                            class="btn btn-success"
                                            form="formPersonaGruppoModifica"
                                        >
                                            Salva
                                        </button>
                                    </x-slot>
                                </x-modal>
                                <!--end modal modifica posizione-->
                            </div>
                        </div>
                    @empty
                        <p class="text-danger">Nessuna azienda</p>
                    @endforelse

                    <x-modal
                        modal-title="Aggiungi Azienda"
                        button-title="Nuova Azienda"
                        button-style="btn-success my-2"
                    >
                        <x-slot:body>
                            <form
                                class="form"
                                method="POST"
                                id="formPersonaAzinedaAggiungi"
                                action="{{ route("nomadelfia.persone.aziende.assegna", ["idPersona" => $persona->id]) }}"
                            >
                                {{ csrf_field() }}
                                <h5 class="my-2">Nuova Azienda</h5>
                                <div class="mb-3 row">
                                    <label
                                        for="staticEmail"
                                        class="col-sm-6 col-form-label"
                                    >
                                        Azienda
                                    </label>
                                    <div class="col-sm-6">
                                        <select
                                            name="azienda_id"
                                            class="form-select"
                                        >
                                            <option value="" selected>
                                                ---seleziona azienda ---
                                            </option>
                                            @foreach (Domain\Nomadelfia\Azienda\Models\Azienda::all() as $azienda)
                                                @if ($persona->aziendeAttuali()->get()->contains(Domain\Nomadelfia\Azienda\Models\Azienda::find(4)) == false)
                                                    <option
                                                        value="{{ $azienda->id }}"
                                                        {{ old("azienda_id") === $azienda->id ? "selected" : "" }}
                                                    >
                                                        {{ $azienda->nome_azienda }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-6 col-form-label">
                                        Data entrata azienda
                                    </label>
                                    <div class="col-sm-6">
                                        <input
                                            class="form-control"
                                            type="date"
                                            name="data_inizio"
                                            value="{{ old("data_inizio") }}"
                                        />
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-6 col-form-label">
                                        Mansione
                                    </label>
                                    <div class="col-sm-6">
                                        <select
                                            name="mansione"
                                            class="form-select"
                                        >
                                            <option value="" selected>
                                                ---seleziona mansione---
                                            </option>
                                            <option value="LAVORATORE">
                                                Lavoratore
                                            </option>
                                            <option
                                                value="RESPONSABILE AZIENDA"
                                            >
                                                Responsabile azienda
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </x-slot>
                        <x-slot:footer>
                            <button
                                class="btn btn-success"
                                form="formPersonaAzinedaAggiungi"
                            >
                                Salva
                            </button>
                        </x-slot>
                    </x-modal>
                    <!--end modal-->
                </div>
                <!--end card body-->
            </div>
            <!--end card -->

            <div class="card my-3">
                <div class="card-header">Storico aziende</div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 fw-bold">Azienda</p>
                        <p class="col-md-3 fw-bold">Data inizio</p>
                        <p class="col-md-3 fw-bold">Data fine</p>
                        <p class="col-md-3 fw-bold">Tempo trascorso</p>
                    </div>
                    @forelse ($persona->aziendeStorico as $aziendaStorico)
                        <div class="row">
                            <p class="col-md-3">
                                {{ $aziendaStorico->nome_azienda }}
                            </p>
                            <p class="col-md-3">
                                {{ $aziendaStorico->pivot->data_inizio_azienda }}
                            </p>
                            <p class="col-md-3">
                                {{ $aziendaStorico->pivot->data_fine_azienda }}
                            </p>
                            <div class="col-md-3">
                                <span class="badge text-bg-info">
                                    {{ Carbon::parse($aziendaStorico->pivot->data_fine_azienda)->diffForHumans(Carbon::parse($aziendaStorico->pivot->data_inizio_azienda), ["short" => true]) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-danger">Nessuna storico</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
