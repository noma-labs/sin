@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Incarichi " . $persona->nominativo])

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Incarichi attuale</div>
                <div class="card-body">
                    @if ($attuali->count() > 0)
                        <div class="row">
                            <p class="col-md-3 font-weight-bold">Incarico</p>
                            <p class="col-md-3 font-weight-bold">Data Inizio</p>
                            <p class="col-md-3 font-weight-bold">
                                Tempo trascorso
                            </p>
                            <p class="col-md-3 font-weight-bold">Op.</p>
                        </div>
                    @endif

                    @forelse ($attuali->get() as $incarico)
                        <div class="row">
                            <p class="col-md-3">
                                @include("nomadelfia.templates.azienda", ["azienda" => $incarico])
                            </p>
                            <p class="col-md-3">
                                {{ $incarico->pivot->data_inizio_azienda }}
                            </p>
                            <div class="col-md-3">
                                <span class="badge badge-info">
                                    @diffHumans($incarico->pivot->data_inizio_azienda)
                                </span>
                            </div>
                            <div class="col-md-3">
                                <x-modal
                                    modal-title="Modifica Incarico attuale"
                                    button-title="Modifica"
                                    button-style="btn-warning my-2"
                                >
                                    <x-slot:body>
                                        <form
                                            class="form"
                                            method="POST"
                                            id="formPersonaGruppoModifica"
                                            action="{{ route("nomadelfia.persone.incarichi.modifica", ["idPersona" => $persona->id, "id" => $incarico->id]) }}"
                                        >
                                            @csrf
                                            <div class="row">
                                                <label
                                                    for="staticEmail"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Incarico attuale
                                                </label>
                                                <div class="col-sm-6">
                                                    <div>
                                                        {{ $incarico->nome_azienda }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Data Inzio
                                                </label>
                                                <div class="col-sm-6">
                                                    <input
                                                        type="date"
                                                        name="data_entrata"
                                                        value="{{ $incarico->pivot->data_inizio_azienda }}"
                                                        class="form-control"
                                                    />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label
                                                    for="inputPassword"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Data fine
                                                </label>
                                                <div class="col-sm-6">
                                                    <input
                                                        type="date"
                                                        name="data_uscita"
                                                        value="{{ $incarico->pivot->data_fine_azienda }}"
                                                        class="form-control"
                                                    />
                                                </div>
                                            </div>

                                            <div class="row">
                                                <label
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Mansione
                                                </label>
                                                <div class="col-sm-6">
                                                    <select
                                                        name="mansione"
                                                        class="form-control"
                                                    >
                                                        <option selected>
                                                            ---seleziona
                                                            mansione---
                                                        </option>
                                                        <option
                                                            value="LAVORATORE"
                                                            {{ $incarico->pivot->mansione == "LAVORATORE" ? "selected" : "" }}
                                                        >
                                                            Lavoratore
                                                        </option>
                                                        <option
                                                            value="RESPONSABILE AZIENDA"
                                                            {{ $incarico->pivot->mansione == "RESPONSABILE AZIENDA" ? "selected" : "" }}
                                                        >
                                                            Responsabile azienda
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
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
                                                            @if($incarico->pivot->stato=='Attivo') checked @endif
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
                                                            @if($incarico->pivot->stato=='Sospeso') checked @endif
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
                                                            @if($incarico->pivot->stato=='Non Attivo') checked @endif
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
                        <p class="text-danger">Nessun incarico</p>
                    @endforelse

                    <x-modal
                        modal-title="Aggiungi Incarico"
                        button-title="Nuovo Incarico"
                        button-style="btn-success my-2"
                    >
                        <x-slot:body>
                            <form
                                class="form"
                                method="POST"
                                id="formPersonaAzinedaAggiungi"
                                action="{{ route("nomadelfia.persone.incarichi.assegna", ["idPersona" => $persona->id]) }}"
                            >
                                @csrf
                                <h5 class="my-2">Nuovo Incarico</h5>
                                <div class="row">
                                    <label
                                        for="staticEmail"
                                        class="col-sm-6 col-form-label"
                                    >
                                        Incarico
                                    </label>
                                    <div class="col-sm-6">
                                        <select
                                            name="azienda_id"
                                            class="form-control"
                                        >
                                            <option value="" selected>
                                                ---seleziona incarico ---
                                            </option>
                                            @foreach ($persona->incarichiPossibili() as $incarico)
                                                <option
                                                    value="{{ $incarico->id }}"
                                                    {{ old("azienda_id") === $incarico->id ? "selected" : "" }}
                                                >
                                                    {{ $incarico->nome_azienda }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">
                                        Data inizio incarico
                                    </label>
                                    <div class="col-sm-6">
                                        <input
                                            type="date"
                                            name="data_inizio"
                                            value="{{ old("data_inizio") }}"
                                            class="form-control"
                                        />
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">
                                        Mansione
                                    </label>
                                    <div class="col-sm-6">
                                        <select
                                            name="mansione"
                                            class="form-control"
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
                                                Responsabile
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
                <div class="card-header">Storico incarichi</div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 font-weight-bold">Incarico</p>
                        <p class="col-md-3 font-weight-bold">Data inizio</p>
                        <p class="col-md-3 font-weight-bold">Data fine</p>
                        <p class="col-md-3 font-weight-bold">Tempo trascorso</p>
                    </div>
                    @forelse ($storico->get() as $incaricoStorico)
                        <div class="row">
                            <p class="col-md-3">
                                {{ $incaricoStorico->nome_azienda }}
                            </p>
                            <p class="col-md-3">
                                {{ $incaricoStorico->pivot->data_inizio_azienda }}
                            </p>
                            <p class="col-md-3">
                                {{ $incaricoStorico->pivot->data_fine_azienda }}
                            </p>
                            <div class="col-md-3">
                                <span class="badge badge-info">
                                    {{ Carbon::parse($incaricoStorico->pivot->data_fine_azienda)->diffForHumans(Carbon::parse($incaricoStorico->pivot->data_inizio_azienda), ["short" => true]) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-danger">Nessuno storico</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
