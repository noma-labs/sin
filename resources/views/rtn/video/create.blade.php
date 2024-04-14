@extends("rtn.index")

@section("title", "Inserisci Video")

@section("archivio")
    <h1>Archivio professionale</h1>

    <form>
        <div class="form-group row">
          <label for="inputDispositivo" class="col-sm-2 col-form-label">Dispositivo</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="inputDispositivo" placeholder="--inserisci dispositivo--">
          </div>
        </div>
        <div class="form-group row">
          <label for="inputCategoria" class="col-sm-2 col-form-label">Categoria</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="inputCategoria" placeholder="--inserisci categoria--">
          </div>
        </div>

        <div class="form-group row">
            <label for="inputPersona" class="col-sm-2 col-form-label">Persona</label>
            <div class="col-sm-10">
                <livewire:search-persona
                    inputName="persona_id"
                    placeholder="Cerca persona"
                    noResultsMessage="Nessun risultato"
                />
            </div>
          </div>


        <div class="form-group row">
          <div class="col-sm-10">
            <button type="submit" class="btn btn-primary">Inserisci</button>
          </div>
        </div>
      </form>
@endsection
