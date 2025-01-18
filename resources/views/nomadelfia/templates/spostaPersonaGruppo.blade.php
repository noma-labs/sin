<my-modal
    modal-title="Sposta in un nuovo Gruppo Familiare"
    button-title="Sposta"
    button-style="btn-success my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="spostaPersonGruppo{{ $persona->id }}"
            action="{{ route("nomadelfia.persone.gruppo.sposta", ["idPersona" => $persona->id, "id" => $attuale->id]) }}"
        >
            {{ csrf_field() }}

            <h5 class="my-2">
                Completa dati del gruppo attuale: {{ $attuale->nome }}
            </h5>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-6 col-form-label">
                    Data uscita gruppo familiare
                </label>
                <div class="col-sm-6">
                    <input type="date" name="current_data_uscita" value="{{ old("current_data_uscita") }}" class="form-control"/>
                    <small id="emailHelp" class="form-text text-muted">
                        Lasciare vuoto se concide con la data di entrata nel
                        nuovo gruppo familiare.
                    </small>
                </div>
            </div>
            <hr />

            <input
                type="hidden"
                name="current_data_entrata"
                value="{{ $attuale->pivot->data_entrata_gruppo }}"
            />

            <h5 class="my-2">Nuovo gruppo familiare</h5>
            <div class="form-group row">
                <label for="staticEmail" class="col-sm-6 col-form-label">
                    Gruppo familiare
                </label>
                <div class="col-sm-6">
                    <select name="new_gruppo_id" class="form-control">
                        <option selected>---seleziona gruppo ---</option>
                        @foreach (Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare::all() as $gruppofam)
                            @if ($gruppofam->id != $attuale->id)
                                <option
                                    value="{{ $gruppofam->id }}"
                                    {{ old("new_gruppo_id") == $gruppofam->id ? "selected" : "" }}
                                >
                                    {{ $gruppofam->nome }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-6 col-form-label">
                    Data entrata gruppo familiare
                </label>
                <div class="col-sm-6">
                    <input type="date" name="new_data_entrata" value="{{ old("new_data_entrata") }}" class="form-control"/>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-success"
            form="spostaPersonGruppo{{ $persona->id }}"
        >
            Salva
        </button>
    </template>
</my-modal>
<!--end modal-->
