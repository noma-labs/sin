@extends("admin.index")
@section("title", "| Modifica ruolo")

@section("content")
    @include("partials.header", ["title" => "Modifica ruolo"])

    <form method="POST" action="{{ route("roles.update", $ruolo->id) }}">
        <input type="hidden" name="_method" value="PUT" />
        @csrf

        <div class="row">
            <div class="col-md-6 offset-md-2">
                <h1>PAGINA IN COSTRUZIONE</h1>
                <p class="fw-normal">
                    Ruolo::
                    <b>{{ $ruolo->name }}</b>
                </p>
            </div>
        </div>
    </form>
@endsection
