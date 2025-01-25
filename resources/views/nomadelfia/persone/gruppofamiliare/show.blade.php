@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Gruppo Familiare  " . $persona->nominativo])

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Gruppo Familiare attuale</div>
                <div class="card-body">
                    @if ($attuale)
                        <div class="row">
                            <p class="col-md-3 fw-bold">
                                Gruppo familiare
                            </p>
                            <p class="col-md-2 fw-bold">
                                Data entrata
                            </p>
                            <p class="col-md-2 fw-bold">
                                Tempo trascorso
                            </p>
                            <p class="col-md-5 fw-bold">Operazioni</p>
                        </div>
                        <div class="row">
                            <p class="col-md-3">{{ $attuale->nome }}</p>
                            <p class="col-md-2">
                                {{ $attuale->pivot->data_entrata_gruppo }}
                            </p>
                            <div class="col-md-2">
                                <span class="badge text-bg-info">
                                    @diffHumans($attuale->pivot->data_entrata_gruppo)
                                </span>
                            </div>
                            <div class="col-md-5">
                                <x-modal
                                    modal-title="Modifica Gruppo familiare attuale"
                                    button-title="Modifica data"
                                    button-style="btn-warning my-2"
                                >
                                    <x-slot:body>
                                        <form
                                            class="form"
                                            method="POST"
                                            id="formPersonaGruppoModifica{{ $attuale->id }}"
                                            action="{{ route("nomadelfia.persone.gruppo.modifica", ["idPersona" => $persona->id, "id" => $attuale->id]) }}"
                                        >
                                            @method("PUT")
                                            @csrf
                                            <input
                                                type="hidden"
                                                name="current_data_entrata"
                                                value="{{ $attuale->pivot->data_entrata_gruppo }}"
                                            />
                                            <div class="row">
                                                <label
                                                    for="staticEmail"
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Gruppo familiare attuale
                                                </label>
                                                <div class="col-sm-6">
                                                    <div>
                                                        {{ $attuale->nome }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label
                                                    class="col-sm-6 col-form-label"
                                                >
                                                    Data entrata
                                                </label>
                                                <div class="col-sm-6">
                                                    <input
                                                        class="form-control"
                                                        type="date"
                                                        name="new_data_entrata"
                                                        value="{{ $attuale->pivot->data_entrata_gruppo }}"
                                                    />
                                                </div>
                                            </div>
                                        </form>
                                    </x-slot>
                                    <x-slot:footer>
                                        <button
                                            class="btn btn-success"
                                            form="formPersonaGruppoModifica{{ $attuale->id }}"
                                        >
                                            Salva
                                        </button>
                                    </x-slot>
                                </x-modal>
                                @include("nomadelfia.templates.spostaPersonaGruppo", ["persona" => $persona, "attuale" => $attuale])
                                @include("nomadelfia.templates.eliminaPersonaDalGruppo", ["persona" => $persona, "gruppo" => $attuale])
                            </div>
                        </div>
                    @else
                        <p class="text-danger">Nessun gruppo familiare</p>
                        @include("nomadelfia.templates.assegnaPersonaNuovoGruppo", ["persona" => $persona])
                    @endif
                </div>
                <!--end card body-->
            </div>
            <!--end card -->
            <div class="card my-3">
                <div class="card-header">Storico dei gruppi familiari</div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 fw-bold">Gruppo</p>
                        <p class="col-md-2 fw-bold">Data inizio</p>
                        <p class="col-md-2 fw-bold">Data fine</p>
                        <p class="col-md-2 fw-bold">Tempo trascorso</p>
                        <p class="col-md-3 fw-bold">Operazioni</p>
                    </div>
                    @forelse ($persona->gruppofamiliariStorico as $gruppostorico)
                        <div class="row">
                            <p class="col-md-3">{{ $gruppostorico->nome }}</p>
                            <p class="col-md-2">
                                {{ $gruppostorico->pivot->data_entrata_gruppo }}
                            </p>
                            <p class="col-md-2">
                                {{ $gruppostorico->pivot->data_uscita_gruppo }}
                            </p>
                            <div class="col-md-2">
                                <span class="badge text-bg-info">
                                    {{ Carbon::parse($gruppostorico->pivot->data_uscita_gruppo)->diffForHumans(Carbon::parse($gruppostorico->pivot->data_entrata_gruppo), ["short" => true]) }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                @include("nomadelfia.templates.eliminaPersonaDalGruppo", ["persona" => $persona, "gruppo" => $gruppostorico])
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
