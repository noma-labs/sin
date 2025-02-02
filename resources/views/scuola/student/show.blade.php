@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Scheda alunno"])

    <div class="card mb-3">
        <div class="card-header">
            {{ $student->nominativo }}
        </div>
     </div>
@endsection
