<form method="GET" action="{{ route("nomadelfia.persone.ricerca.submit") }}">
    @csrf
    <div class="row">
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
            <div class="">
                <label class="form-label">Cognome</label>
                <input
                    class="form-control"
                    name="cognome"
                    type="text"
                    placeholder="Inserisci il cognome"
                />
            </div>
        </div>

        <div class="col-md-2">
            <div class="">
                <label class="form-label">Data di Nascita</label>
                <select
                    class="form-control"
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
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <input
                type="date"
                class="form-control"
                id="data_nascita"
                name="data_nascita"
            />
        </div>
    </div>
    <div class="row align-items-end">
        <div class="col-md-2 offset-md-8">
            <div class="">
                <label id="lab">&nbsp;</label>
                <button type="submit" class="btn btn-primary">Ricerca</button>
            </div>
        </div>
    </div>
</form>
