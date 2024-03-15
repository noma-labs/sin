<my-modal
    modal-title="Assegna Gruppo Familiare"
    button-title="Nuovo Gruppo"
    button-style="btn-success my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formPersonaGruppo"
            action="{{ route("nomadelfia.persone.gruppo.assegna", ["idPersona" => $persona->id]) }}"
        >
            {{ csrf_field() }}

            <h5 class="my-2">Nuovo gruppo familiare</h5>
            <div class="form-group row">
                <label for="staticEmail" class="col-sm-6 col-form-label">
                    Gruppo familiare
                </label>
                <div class="col-sm-6">
                    <select name="gruppo_id" class="form-control">
                        <option selected>---seleziona gruppo ---</option>
                        @foreach (Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare::all() as $gruppofam)
                            <option
                                value="{{ $gruppofam->id }}"
                                {{ old("gruppo_id") == $gruppofam->id ? "selected" : "" }}
                            >
                                {{ $gruppofam->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-6 col-form-label">
                    Data entrata gruppo familiare
                </label>
                <div class="col-sm-6">
                    <date-picker
                        :bootstrap-styling="true"
                        value="{{ old("data_entrata") }}"
                        format="yyyy-MM-dd"
                        name="data_entrata"
                    ></date-picker>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button class="btn btn-success" form="formPersonaGruppo">Salva</button>
    </template>
</my-modal>
<!--end modal-->
