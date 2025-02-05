@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi copertina per thumbnail"])

    <form action="{{route('scuola.elaborati.thumbnail.store', $elaborato->id)}}" method="POST"    enctype="multipart/form-data">
        @csrf
        <input type="file" id="file" accept="image/png" name="file" />

    <button type="submit">Genera</button>
</form>

@endsection
