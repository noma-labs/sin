@include("partials.header", ["title" => "Gestione DVD")])


<form method="GET" class="form" action="{{ route("video.ricerca.submit") }}">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <label for="xTitolo" class="form-label">Collocazione DVD</label>
            <input
                class="form-control"
                type="text"
                name="cassetta"
                placeholder="Inserisci collocazione DVD (ex. ZA)"
            />
        </div>

        <div class="col-md-4">
            <div class="">
                <label for="xClassificazione" class="form-label">
                    Data registrazione
                </label>
                <input
                    class="form-control"
                    type="text"
                    name="data_registrazione"
                    placeholder="Inserisci data registrazione"
                />
            </div>
        </div>
        <div class="col-md-4">
            <div class="">
                <label for="xNote" class="form-label">Descrizione</label>
                <input
                    class="form-control"
                    name="descrizione"
                    type="text"
                    placeholder="Inserisci parola da ricercare nelle descrizione"
                />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="btn-toolbar pull-right">
                <div>
                    <button
                        class="btn btn-success"
                        id="biblio"
                        name="biblioteca"
                        type="submit"
                    >
                        Cerca DVD
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
