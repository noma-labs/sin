<my-modal
    modal-title="Aggiungi Filtro"
    button-title="Aggiungi Filtro"
    button-style="btn-block btn-warning"
>
    <template slot="modal-body-slot">
        <form
            method="POST"
            action="{{ route("filtri.aggiungi") }}"
            id="form-aggiungi-filtro"
        >
            {{ csrf_field() }}
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
                    <select name="tipo" id="tipo" class="form-control">
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
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-success"
            type="submit"
            form="form-aggiungi-filtro"
        >
            Salva
        </button>
    </template>
</my-modal>
