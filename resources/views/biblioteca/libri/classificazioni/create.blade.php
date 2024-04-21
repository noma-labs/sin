@extends("biblioteca.libri.index")
@section("title", "Aggiungi Classificazione")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Classificazione"])

    <div class="row">
        <div class="col-md-4 offset-md-4">
            {{ Form::open(["route" => ["classificazioni.index"], "class" => "form-horizontal"]) }}
            <div class="form-group">
                {{ Form::label("descrizione", "Classificazione") }}
                {{ Form::text("descrizione", null, ["class" => "form-control", "placeholder" => "Nome classificazione"]) }}
            </div>
            <div class="form-group">
                {{ Form::submit("Aggiungi", ["class" => "btn btn-primary"]) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
