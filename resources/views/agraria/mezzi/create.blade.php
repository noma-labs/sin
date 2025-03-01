@extends("agraria.index")
@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => "Inserimento mezzo agricolo"])
    <form action="{{ route("agraria.vehicle.store") }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label" for="nome">Nome:</label>
                <input type="text" class="form-control" name="nome" />
            </div>
            <div class="col-md-4">
                <label class="form-label" for="nome">Numero di Telaio:</label>
                <input type="text" class="form-control" name="telaio" />
            </div>
            <div class="col-md-4">
                <label class="form-label" for="nome">Ore Totali:</label>
                <input type="number" class="form-control" name="ore" />
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label" for="nome">Gomme Anteriori:</label>
                <input name="gomme_ant" type="text" class="form-control" />
            </div>
            <div class="col-md-3">
                <label class="form-label" for="nome">Gomme Posteriori:</label>
                <input name="gomme_post" type="text" class="form-control" />
            </div>
            <div class="col-md-3">
                <label class="form-label" for="nome">
                    Filtro Aria Interno:
                </label>
                <input name="aria_int" type="text" class="form-control" />
            </div>
            <div class="col-md-3">
                <label class="form-label" for="nome">
                    Filtro Aria Esterno:
                </label>
                <input name="aria_ext" type="text" class="form-control" />
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label" for="nome">Filtro Olio:</label>
                <input name="olio" type="text" class="form-control" />
            </div>
            <div class="col-md-4">
                <label class="form-label" for="nome">Filtro Gasolio:</label>
                <input name="gasolio" type="text" class="form-control" />
            </div>
            <div class="col-md-4">
                <label class="form-label" for="nome">Filtro Serviz:</label>
                <input name="servizi" type="text" class="form-control" />
            </div>
        </div>
        <div class="justify-content-end">
            <button class="btn btn-success" type="submit">Salva</button>
        </div>
    </form>
@endsection
