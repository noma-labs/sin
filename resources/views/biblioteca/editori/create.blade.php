@extends("biblioteca.libri.index")
@section("title", "Aggiungi Editore")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Editore"])

    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-2">
                {{ Form::open(["route" => ["editori.store"], "class" => "form-horizontal"]) }}
                <div class="form-group">
                    {{ Form::label("editore", "Editore") }}
                    {{ Form::text("editore", null, ["class" => "form-control", "placeholder" => "Es. Mondadori"]) }}
                </div>
                <div class="form-group my-3">
                    {{ Form::submit("Aggiungi", ["class" => "btn btn-primary"]) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
