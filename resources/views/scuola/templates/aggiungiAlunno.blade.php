<my-modal
    modal-title="Aggiungi Alunno"
    button-title="Aggiungi Alunno"
    button-style="btn-primary my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formComponente"
            action="{{ route("scuola.classi.alunno.assegna", ["id" => $classe->id]) }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Alunno
                </label>
                <div class="col-8">
                    @if ($alunniPossibili->isNotEmpty())
                        <select
                            name="alunno_id[]"
                            id="alunno_id"
                            class="form-control"
                            multiple
                            size="{{ $alunniPossibili->count() }}"
                        >
                            @foreach ($alunniPossibili as $alunno)
                                <option value="{{ $alunno->id }}">
                                    {{ Carbon::createFromFormat("Y-m-d", $alunno->data_nascita)->year . " " . $alunno->nome . " " . $alunno->cognome }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <p class="text-danger">Nessun alunno</p>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data Inizio
                </label>
                <div class="col-8">
                    <date-picker
                        :bootstrap-styling="true"
                        value="{{ old("data_inizio") }}"
                        format="yyyy-MM-dd"
                        name="data_inizio"
                    ></date-picker>
                    <small id="emailHelp" class="form-text text-muted">
                        Lasciare vuoto se coincide con la data di inizio anno
                        scolastico.
                    </small>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button class="btn btn-danger" form="formComponente">Salva</button>
    </template>
</my-modal>
