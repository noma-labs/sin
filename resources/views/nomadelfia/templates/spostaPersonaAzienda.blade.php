<my-modal
    modal-title="Sposta Persona in Azienda"
    button-title="Sposta"
    button-style="btn-success my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="spostaPersonAzienda{{ $lavoratore->id }}"
            action="{{ route("nomadelfia.azienda.sposta", ["id" => $azienda->id, "idPersona" => $lavoratore->id]) }}"
        >
            {{ csrf_field() }}

            <div class="form-group row">
                <label for="inputData" class="col-sm-6 col-form-label">
                    Data fine
                </label>
                <div class="col-sm-6">
                    <date-picker
                        :bootstrap-styling="true"
                        value="{{ old("data_fine") }}"
                        format="yyyy-MM-dd"
                        name="data_fine"
                    ></date-picker>
                </div>
            </div>
            <hr />


            <div class="form-group row">
                <label for="staticEmail" class="col-sm-6 col-form-label">
                    Nuova azienda
                </label>
                <div class="col-sm-6">
                    <select name="nuova_azienda_id" class="form-control">
                        <option selected>---seleziona azienda ---</option>
                        @foreach (Domain\Nomadelfia\Azienda\Models\Azienda::orderBy('nome_azienda')->get() as $a)
                            @if ($a->id != $azienda->id)
                                <option
                                    value="{{ $a->id }}"
                                    {{ old("nuova_azienda_id") == $a->id ? "selected" : "" }}
                                >
                                    {{ $a->nome_azienda }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-success"
            form="spostaPersonAzienda{{ $lavoratore->id }}"
        >
            Salva
        </button>
    </template>
</my-modal>
