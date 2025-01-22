@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Persona"])

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <form
                method="POST"
                action="{{ route("nomadelfia.persone.store") }}"
            >
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <input
                            class="form-control"
                            id="forpersona"
                            name="persona"
                            value="{{ old("persona") }}"
                            placeholder="---Inserisci Nominativo o Nome o Cognome---"
                        />
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-success mt-2" type="submit">
                            Cerca persone
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
