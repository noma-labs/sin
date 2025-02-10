<form method="GET" action="{{ route("nomadelfia.persone.ricerca.submit") }}">
    @csrf
    <div class="row mb-3 g-3">
        <div class="col-md-2">
            <label class="form-label">Nominativo</label>
            <input
                class="form-control"
                name="nominativo"
                type="text"
                placeholder="Inserisci il nominativo..."
            />
        </div>
        <div class="col-md-2">
            <label class="form-label">Nome</label>
            <input
                class="form-control"
                name="nome"
                type="text"
                placeholder="Inserisci il nome."
            />
        </div>

        <div class="col-md-2">
            <label class="form-label">Cognome</label>
            <input
                class="form-control"
                name="cognome"
                type="text"
                placeholder="Inserisci il cognome"
            />
        </div>

        <div class="col-md-2">
            <div class="">
                <label class="form-label">Data di Nascita</label>
                <select
                    class="form-select"
                    name="criterio_data_nascita"
                    type="text"
                >
                    <option value="<">Minore</option>
                    <option value="<=" selected>Minore Uguale</option>
                    <option value="=">Uguale</option>
                    <option value=">">Maggiore</option>
                    <option value=">=">Maggiore Uguale</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <input
                type="date"
                class="form-control"
                id="data_nascita"
                name="data_nascita"
            />
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Ricerca</button>
        </div>
    </div>
</form>
