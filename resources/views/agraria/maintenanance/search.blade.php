@extends("agraria.index")

@section("title", "Manutenzione")

@section("content")
    @include("partials.header", ["title" => "Ricerca Manutenzione"])
    <div class="col form-group">
        <label for="mezzo">Mezzo</label>
        <select name="mezzo" class="form-control" id="mezzo">
            <option hidden>-- Seleziona --</option>
            @foreach ($mezzi as $m)
                <option value="{{ $m->id }}">{{ $m->nome }}</option>
            @endforeach
        </select>
    </div>
@endsection
