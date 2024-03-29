<my-modal
    modal-title="Sposta in un nuovo gruppo familiare"
    button-style="btn-warning my-2"
    button-title="Sposta"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formAssegnaGruppo{{ $famiglia_id }}"
            action="{{ route("nomadelfia.famiglie.gruppo.sposta", ["id" => $famiglia_id, "currentGruppo" => $gruppo_id]) }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Nuovo gruppo
                </label>
                <div class="col-8">
                    <select class="form-control" name="nuovo_gruppo_id">
                        <option value="" selected>---scegli gruppo---</option>
                        @foreach (Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare::all() as $gruppo)
                            @if ($gruppo->id != $gruppo_id)
                                <option value="{{ $gruppo->id }}">
                                    {{ $gruppo->nome }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data cambio gruppo:
                </label>
                <div class="col-8">
                    <date-picker
                        :bootstrap-styling="true"
                        value="{{ old("data_cambiogruppo") }}"
                        format="yyyy-MM-dd"
                        name="data_cambiogruppo"
                    ></date-picker>
                </div>
            </div>
            <div class="form-group row">
                <div class="col"></div>
                <div class="text-justify">
                    Le seguenti persone saranno spostate nel gruppo familiare
                    selezionato:
                    <ul>
                        @foreach ($componenti as $componente)
                            <li>{{ $componente->nominativo }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-success"
            form="formAssegnaGruppo{{ $famiglia_id }}"
        >
            Salva
        </button>
    </template>
</my-modal>
