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
            <form action="{{ route("nomadelfia.persone.inserimento.entrata.scelta.view",  ["idPersona" => $persona->id]) }}" method="POST">
                @csrf
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-6 pt-0">
                            Origine (prima entrata in Nomadelfia):
                        </legend>
                        <div class="col-sm-6">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="origine"
                                    id="nascita"
                                    value="nascita"
                                    @if(old('nascita')=='M') checked @endif
                                />
                                <label class="form-check-label" for="nascita">
                                    Nato in Nomadelfia
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="origine"
                                    id="accolto"
                                    value="accolto"
                                    @if(old('sesso')=='F') checked @endif
                                />
                                <label class="form-check-label" for="accolto">
                                    Figlio accolto
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="origine"
                                    id="famiglia"
                                    value="famiglia"
                                    @if(old('sesso')=='F') checked @endif
                                />
                                <label class="form-check-label" for="famiglia">
                                    Minorenne entrato con la sua famiglia
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="origine"
                                    id="famiglia"
                                    value="esterno"
                                    @if(old('sesso')=='F') checked @endif
                                />
                                <label class="form-check-label" for="famiglia">
                                    Esterno
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
            <button type="submit" class="btn btn-primary">Salva</button>
        </form>
        <

@endsection
