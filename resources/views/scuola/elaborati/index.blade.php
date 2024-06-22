@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Elaborati"])
    <a
        class="btn btn-primary"
        href="{{ route("scuola.elaborati.create") }}"
        role="button"
    >
        Inserisci
    </a>
@endsection
