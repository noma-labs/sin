@extends("biblioteca.libri.index")
@section("title", "Editori")

@section("content")
    @include("partials.header", ["title" => "Modifica Editore"])

    <div class="row">
        <div class="col-md-6 offset-md-2">
            <form action="{{ route('editori.update', $editore->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="editore">Editore</label>
                    <input type="text" name="editore" id="editore" class="form-control" value="{{ $editore->editore }}" />
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
@endsection
