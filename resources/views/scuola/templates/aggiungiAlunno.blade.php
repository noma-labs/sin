<x-modal
    modal-title="Aggiungi Alunno"
    button-title="Aggiungi Alunno"
    button-style="btn-primary my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formComponente"
            action="{{ route("scuola.classi.alunno.assegna", ["id" => $classe->id]) }}"
        >
            @csrf
            <div class="mb-3">
                <label for="example-text-input" class="form-label">
                    Alunno
                </label>
                @if ($alunniPossibili->isNotEmpty())
                    <select
                        name="alunno_id[]"
                        id="alunno_id"
                        class="form-select"
                        multiple
                        size="{{ min($alunniPossibili->count(), 20) }}"
                    >
                        @foreach ($alunniPossibili as $alunno)
                            <option value="{{ $alunno->id }}">
                                {{ "(" . Carbon::createFromFormat("Y-m-d", $alunno->data_nascita)->year . ") " . $alunno->nome . " " . $alunno->cognome . " (" . $alunno->nominativo. ")" }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <p class="text-danger">Nessun alunno</p>
                @endif
            </div>
            <div class="row">
                <label for="example-text-input" class="col-4 form-label">
                    Data Inizio
                </label>
                <div class="col-8">
                    <input
                        type="date"
                        name="data_inizio"
                        value="{{ old("data_inizio") }}"
                        class="form-control"
                    />
                    <small id="emailHelp" class="form-text text-muted">
                        Lasciare vuoto se coincide con la data di inizio anno
                        scolastico.
                    </small>
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger" form="formComponente">Salva</button>
    </x-slot>
</x-modal>
