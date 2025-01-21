<x-modal
    modal-title="Aggiungi Coordinatore"
    button-title="Aggiungi coordinatore"
    button-style="btn-primary my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formAggiungiCoord"
            action="{{ route("scuola.classi.coordinatore.assegna", ["id" => $classe->id]) }}"
        >
            {{ csrf_field() }}
            <div class="mb-3 row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Coordinatore
                </label>
                <div class="col-8">
                    <select class="form-select" name="coord_id">
                        <option value="" selected>
                            ---scegli coordinatore--
                        </option>
                        @foreach ($coordPossibili as $p)
                            <option value="{{ $p->id }}">
                                @if ($p->nominativo != $p->nome)
                                    {{ $p->nome . " " . $p->cognome . " (" . $p->nominativo . ")" }}
                                @else
                                    {{ $p->nome . " " . $p->cognome }}
                                @endif
                                {{ "(" . Carbon::createFromFormat("Y-m-d", $p->data_nascita)->year . ") " }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Tipo
                </label>
                <div class="col-8">
                    <select class="form-select" name="coord_tipo">
                        <option value="" selected>---scegli tipo--</option>
                        @foreach (App\Scuola\Models\Coordinatore::getPossibleEnumValues("tipo", "db_scuola.coordinatori_classi") as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="example-text-input" class="col-4 col-form-label">
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
                        Lasciare vuoto se concide con la data di inzio anno
                        scolastico.
                    </small>
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger" form="formAggiungiCoord">Salva</button>
    </x-slot>
</x-modal>
