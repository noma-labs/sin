<x-modal
    modal-title="Aggiungi Filtro"
    button-title="Aggiungi Filtro"
    button-style=" btn-warning"
>
    <x-slot:body>
        <form
            method="POST"
            action="{{ route("filtri.aggiungi") }}"
            id="form-aggiungi-filtro"
        >
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <label for="codice">Codice Filtro</label>
                    <input
                        name="codice"
                        type="text"
                        id="codice"
                        class="form-control"
                        placeholder="es. S 3259"
                    />
                </div>
                <div class="col-md-4">
                    <label for="tipo">Tipo</label>
                    <select name="tipo" id="tipo" class="form-select">
                        <option hidden selected>--Seleziona--</option>
                        @foreach (App\Officina\Models\TipoFiltro::tipo() as $t_filtro)
                            <option value="{{ $t_filtro }}">
                                {{ $t_filtro }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="note">Note</label>
                    <input
                        type="text"
                        name="note"
                        id="note"
                        class="form-control"
                        value=""
                        placeholder="Nota Facoltativa..."
                    />
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-success"
            type="submit"
            form="form-aggiungi-filtro"
        >
            Salva
        </button>
    </x-slot>
</x-modal>
