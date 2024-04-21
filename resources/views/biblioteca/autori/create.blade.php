@extends("biblioteca.libri.index")
@section("title", "Aggiungi Autore")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Autore"])
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-2">
                {{ Form::open(["url" => route("autori.store"), "class" => "form-horizontal"]) }}
                <div class="form-group">
                    {{ Form::label("autore", "Autore") }}
                    {{ Form::text("autore", null, ["class" => "form-control", "placeholder" => "Es. Italo Calvino"]) }}
                </div>
                <div class="form-group my-3">
                    {{ Form::submit("Aggiungi", ["class" => "btn btn-primary"]) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
