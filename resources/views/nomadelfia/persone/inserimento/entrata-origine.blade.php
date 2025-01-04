@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Entrata Persona"])

    <div class="form-group row offset-md-4">
        <label class="col-sm-2">Nome</label>
        <div class="col-sm-10">
          <p class="font-weight-bold">{{ $persona->nome }}</p>
        </div>
      </div>
      <div class="form-group row offset-md-4">
        <label class="col-sm-2">Cognome</label>
        <div class="col-sm-10">
          <p class="font-weight-bold">{{ $persona->cognome }}</p>
        </div>
      </div>

      <div class="form-group row offset-md-4">
        <label class="col-sm-2">Data nascita</label>
        <div class="col-sm-10">
          <p class="font-weight-bold">{{ $persona->data_nascita }}</p>
        </div>
      </div>

      <div class="form-group row offset-md-4">
        <label class="col-sm-2">Come è entrato in Nomadelfia?</label>
        <div class="col-sm-4">
            <form action="{{ route("nomadelfia.persone.inserimento.entrata.scelta.view",  ["idPersona" => $persona->id]) }}" method="POST">
                @csrf
            <select class="form-control" name="origine">
                <option value="">--- Seleziona origine ---</option>
                <option value="interno">Nato a Nomadelfia</option>
                <option value="accolto">Minorenne Accolto</option>
                <option value="minorenne_famiglia">Minorenne entrato con la sua famiglia</option>
                <option value="esterno">Maggiorenne (single o con famiglia)</option>
            </select>
            <button type="submit" class="btn btn-primary">Salva</button>
        </form>
        </div>
      </div>

@endsection
