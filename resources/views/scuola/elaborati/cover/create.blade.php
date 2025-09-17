@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi copertina"])

    <form
        action="{{ route("scuola.elaborati.cover.store", $elaborato->id) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="mb-3">
            <label for="file" class="form-label">
                Scegli file per la copertina
            </label>
            <input type="file" id="file" accept="image/png" name="file" />
        </div>
        <button type="submit" class="btn btn-primary">Carica</button>
    </form>
@endsection
