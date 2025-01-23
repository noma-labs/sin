@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Posizione  " . $persona->nominativo])

    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- card posizione attuale -->
            <div class="card">
                <div class="card-header">Posizione attuale</div>
                <div class="card-body">
                    @if ($posattuale != null)
                        <div class="row">
                            <p class="col-md-3 font-weight-bold">Posizione</p>
                            <p class="col-md-2 font-weight-bold">Data Inizio</p>
                            <p class="col-md-2 font-weight-bold">
                                Tempo trascorso
                            </p>
                            <p class="col-md-5 font-weight-bold">Operazioni</p>
                        </div>
                        <div class="row">
                            <p class="col-md-3">{{ $posattuale->nome }}</p>
                            <p class="col-md-2">
                                {{ $posattuale->pivot->data_inizio }}
                            </p>
                            <div class="col-md-2">
                                <span class="badge badge-info">
                                    @diffHumans($posattuale->pivot->data_inizio)
                                </span>
                            </div>
                            <div class="col-md-5">
                                @include("nomadelfia.templates.modificaDataPosizione", ["persona" => $persona, "id" => $posattuale->id, "nome" => $posattuale->nome, "data_inizio" => $posattuale->pivot->data_inizio])

                                <x-modal
                                    modal-title="Concludi Posizione"
                                    button-title="Concludi"
                                    button-style="btn-info my-2"
                                >
                                    <x-slot:body>
                                        <form
                                            class="form"
                                            method="POST"
                                            id="formConcludiPosizione{{ $posattuale->id }}"
                                            action="{{ route("nomadelfia.persone.posizione.concludi", ["idPersona" => $persona->id, "id" => $posattuale->id]) }}"
                                        >
                                            @csrf
                                            <input
                                                type="hidden"
                                                name="data_inizio"
                                                value="{{ $posattuale->pivot->data_inizio }}"
                                            />
                                            <div class="row">
                                                <label
                                                    for="staticEmail"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    posizione familiare attuale
                                                </label>
                                                <div class="col-sm-6">
                                                    <div>
                                                        {{ $posattuale->nome }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <label
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Data fine posizione
                                                </label>
                                                <div class="col-sm-6">
                                                    <input
                                                        type="date"
                                                        name="data_fine"
                                                        class="form-control"
                                                        value="{{ $posattuale->pivot->data_fine }}"
                                                    />
                                                </div>
                                            </div>
                                        </form>
                                    </x-slot>
                                    <x-slot:footer>
                                        <button
                                            class="btn btn-success"
                                            form="formConcludiPosizione{{ $posattuale->id }}"
                                        >
                                            Salva
                                        </button>
                                    </x-slot>
                                </x-modal>

                                @include("nomadelfia.templates.eliminaPersonaPosizione", ["persona" => $persona, "posizione" => $posattuale])
                            </div>
                        </div>
                    @else
                        <p class="text-danger">Nessuna posizione</p>
                    @endif
                    <x-modal
                        modal-title="Aggiungi Posizione persona"
                        button-title="Nuova Posizione"
                        button-style="btn-success  my-2"
                    >
                        <x-slot:body>
                            <form
                                class="form"
                                method="POST"
                                id="formPersonaPosizione"
                                action="{{ route("nomadelfia.persone.posizione.assegna", ["idPersona" => $persona->id]) }}"
                            >
                                @csrf
                                @if ($posattuale != null)
                                    <h5 class="my-2">
                                        Completa dati della posizione attuale:
                                        {{ $posattuale->nome }}
                                    </h5>
                                    <div class="row">
                                        <label
                                            for="inputPassword"
                                            class="col-sm-6 col-form-label"
                                        >
                                            Data fine posizione
                                        </label>
                                        <input
                                            type="date"
                                            name="data_fine"
                                            class="form-control"
                                            value="{{ Carbon::now()->toDateString() }}"
                                        />
                                        <small
                                            id="emailHelp"
                                            class="form-text text-muted"
                                        >
                                            Lasciare vuoto se concide con la
                                            data di inizio della nuova posizione
                                            .
                                        </small>
                                    </div>
                                @endif

                                <h5 class="my-2">
                                    Inserimento nuova posizione
                                </h5>
                                <div class="row">
                                    <label
                                        for="staticEmail"
                                        class="col-sm-6 col-form-label"
                                    >
                                        Posizione
                                    </label>
                                    <div class="col-sm-6">
                                        <select
                                            name="posizione_id"
                                            class="form-control"
                                        >
                                            <option value="" selected>
                                                ---seleziona posizione---
                                            </option>
                                            @foreach ($persona->posizioniPossibili() as $posizione)
                                                <option
                                                    value="{{ $posizione->id }}"
                                                >
                                                    {{ $posizione->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">
                                        Data inizio
                                    </label>
                                    <div class="col-sm-6">
                                        <input
                                            type="date"
                                            name="data_inizio"
                                            class="form-control"
                                            value="{{ old("data_inizio") ? old("data_inizio") : Carbon::now()->toDateString() }}"
                                        />
                                    </div>
                                </div>
                            </form>
                        </x-slot>
                        <x-slot:footer>
                            <button
                                class="btn btn-success"
                                form="formPersonaPosizione"
                            >
                                Salva
                            </button>
                        </x-slot>
                    </x-modal>
                    <!--end modal aggiungi posizione-->
                </div>
                <!--end card body-->
            </div>
            <!--end card -->

            <!-- card posizione storico -->
            <div class="card my-3">
                <div class="card-header">Storico delle Posizione</div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-2 font-weight-bold">Posizione</p>
                        <p class="col-md-2 font-weight-bold">Data inizio</p>
                        <p class="col-md-2 font-weight-bold">Data fine</p>
                        <p class="col-md-2 font-weight-bold">Durata</p>
                        <p class="col-md-4 font-weight-bold">Operazioni</p>
                    </div>

                    @forelse ($storico as $posizionestor)
                        <div class="row">
                            <p class="col-md-2">{{ $posizionestor->nome }}</p>
                            <p class="col-md-2">
                                {{ $posizionestor->pivot->data_inizio }}
                            </p>
                            <p class="col-md-2">
                                {{ $posizionestor->pivot->data_fine }}
                            </p>

                            <div class="col-md-2">
                                <span class="badge badge-info">
                                    {{ Carbon::parse($posizionestor->pivot->data_fine)->diffForHumans(Carbon::parse($posizionestor->pivot->data_inizio), ["short" => true]) }}
                                </span>
                            </div>
                            <div class="col-md-2">
                                @include("nomadelfia.templates.eliminaPersonaPosizione", ["persona" => $persona, "posizione" => $posizionestor])
                            </div>
                        </div>
                    @empty
                        <p class="text-danger">
                            Nessuna posizione nello storico
                        </p>
                    @endforelse
                </div>
                <!--end card body-->
            </div>
            <!--end card -->
        </div>
        <!--end card -->
    </div>
@endsection
