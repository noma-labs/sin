@extends("rtn.index")

@section("title", "Inserisci Video")

@section("archivio")
    @include("partials.header", ["title" => "Aggiungi Video Archivio Professionale"])
    <form method="post" action="{{ route("rtn.video.store") }}">
        {{ csrf_field() }}
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="forDispositivo">Dispositivo</label>
                <select
                    class="form-control"
                    id="validatedInputGroupSelect"
                    required
                >
                    <option value="">Choose...</option>
                    <option value="1">Hard-disk</option>
                    <option value="2">Altro</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="forCategoria">Categoria</label>
                <input type="password" class="form-control" id="forCategoria" />
            </div>

            <div class="form-group col-md-4">
                <label for="forCategoriaEvento">Categoria Evento</label>
                <input
                    type="password"
                    class="form-control"
                    id="forCategoriaEvento"
                />
            </div>

            <div class="form-group col-md-2">
                <label for="forRecord">Record</label>
                <input type="number" class="form-control" id="forRecord" />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="inputInizioMinuti">Inizio Minuti</label>
                <input
                    type="number"
                    class="form-control"
                    id="inputInizioMinuti"
                    placeholder="--inserisci minuti"
                />
            </div>
            <div class="form-group col-md-2">
                <label for="inputFineMinuti">Fine Minuti</label>
                <input
                    type="number"
                    class="form-control"
                    id="inputFineMinuti"
                    placeholder="--inserisci minuti"
                />
            </div>
            <div class="form-group col-md-4">
                <label for="forDataRegistrazione">Data Registrazione</label>
                <input
                    type="date"
                    class="form-control"
                    id="forDataRegistrazione"
                    placeholder="--inserisci categoria--"
                />
            </div>
            <div class="form-group col-md-4">
                <label for="forDataTrasmission">Data Ultima trasmissione</label>
                <input
                    type="date"
                    class="form-control"
                    id="forDataTrasmission"
                    placeholder="--inserisci categoria--"
                />
            </div>
        </div>


        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputFineMinuti">Località</label>
                <input
                    type="number"
                    class="form-control"
                    id="inputFineMinuti"
                    placeholder="--inserisci Località"
                />
            </div>
            <div class="form-group col-md-6">
                <label for="forDataRegistrazione">Argomento</label>
                <input
                    type="date"
                    class="form-control"
                    id="forDataRegistrazione"
                    placeholder="--inserisci Argomento"
                />
            </div>
        </div>



        <div class="form-group row">
            <div class="form-group col-md-12">
            <label for="inputPersona">           Persone </label>
            <livewire:search-persona
                placeholder="Cerca persona"
                noResultsMessage="Nessun risultato"
            />
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">Inserisci</button>
            </div>
        </div>
    </form>
@endsection
