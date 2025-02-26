@extends("agraria.index")
@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => "Nuovo Mezzo"])
    <div class="mx-auto" style="width: 250px">
        <foto-mezzo></foto-mezzo>
    </div>
    <form action="{{ route("agraria.vehicle.store") }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" class="form-control" name="nome" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Numero di Telaio:</label>
                    <input type="text" class="form-control" name="telaio" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Ore Totali:</label>
                    <input type="number" class="form-control" name="ore" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nome">Gomme Anteriori:</label>
                    <input name="gomme_ant" type="text" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nome">Gomme Posteriori:</label>
                    <input name="gomme_post" type="text" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nome">Filtro Aria Interno:</label>
                    <input name="aria_int" type="text" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nome">Filtro Aria Esterno:</label>
                    <input name="aria_ext" type="text" class="form-control" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Filtro Olio:</label>
                    <input name="olio" type="text" class="form-control" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Filtro Gasolio:</label>
                    <input name="gasolio" type="text" class="form-control" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Filtro Serviz:</label>
                    <input name="servizi" type="text" class="form-control" />
                </div>
            </div>
        </div>
        <div class="justify-content-end">
            <button class="btn btn-success btn-block" type="submit">
                Salva
            </button>
        </div>
    </form>
@endsection
