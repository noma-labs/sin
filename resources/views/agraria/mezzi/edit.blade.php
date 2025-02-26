@extends("agraria.index")

@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => $mezzo->nome])
    <form action="{{ route("agraria.vehicle.update") }}" method="POST">
        @csrf
        @method("PUT")
        <input type="hidden" value="{{ $mezzo->id }}" name="id" />
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ $mezzo->nome }}"
                        name="nome"
                    />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Numero di Telaio:</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ $mezzo->numero_telaio }}"
                        name="telaio"
                    />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Ore Totali:</label>
                    <input
                        type="number"
                        class="form-control"
                        value="{{ $mezzo->tot_ore }}"
                        name="ore"
                    />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nome">Gomme Anteriori:</label>
                    <input
                        name="gomme_ant"
                        type="text"
                        class="form-control"
                        @if ($mezzo->gomme_ant)
                            value="{{ $mezzo->gommeAnt->nome }}"
                        @endif
                    />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nome">Gomme Posteriori:</label>
                    <input
                        name="gomme_post"
                        type="text"
                        class="form-control"
                        @if ($mezzo->gomme_post)
                            value="{{ $mezzo->gommePos->nome }}"
                        @endif
                    />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nome">Filtro Aria Interno:</label>
                    <input
                        name="aria_int"
                        type="text"
                        class="form-control"
                        value="{{ $mezzo->filtro_aria_int }}"
                    />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nome">Filtro Aria Esterno:</label>
                    <input
                        name="aria_ext"
                        type="text"
                        class="form-control"
                        value="{{ $mezzo->filtro_aria_ext }}"
                    />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Filtro Olio:</label>
                    <input
                        name="olio"
                        type="text"
                        class="form-control"
                        value="{{ $mezzo->filtro_olio }}"
                    />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Filtro Gasolio:</label>
                    <input
                        name="gasolio"
                        type="text"
                        class="form-control"
                        value="{{ $mezzo->filtro_gasolio }}"
                    />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nome">Filtro Serviz:</label>
                    <input
                        name="servizi"
                        type="text"
                        class="form-control"
                        value="{{ $mezzo->filtro_servizi }}"
                    />
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
