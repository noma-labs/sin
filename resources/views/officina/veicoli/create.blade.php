@extends("officina.index")

@section("content")
    @include("partials.header", ["title" => "Nuovo Veicolo"])

    <form method="post" action="{{ route("veicoli.create") }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-3">
                <label for="nome">Nome</label>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Nome Veicolo..."
                    name="nome"
                    value="{{ old("nome") }}"
                />
            </div>
            <div class="col-md-3">
                <label for="targa">Targa</label>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Targa Veicolo"
                    name="targa"
                    value="{{ old("targa") }}"
                />
            </div>
            <div class="col-md-3">
                <label for="marca">Marca</label>
                <select class="form-control" id="marca" name="marca">
                    <option hidden disabled selected value="">Scegli...</option>
                    @foreach ($marche as $marca)
                        <option
                            value="{{ $marca->id }}"
                            @if($marca->id == old('marca')) selected @endif
                        >
                            {{ $marca->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="alimentazione">Modello</label>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Es. Doblò, Marea..."
                    name="modello"
                />
            </div>

            <div class="col-md-3">
                <label for="impiego">Impiego</label>
                <select class="form-control" id="impiego" name="impiego">
                    <option hidden disabled selected value="">Scegli...</option>
                    @foreach ($impieghi as $impiego)
                        <option
                            value="{{ $impiego->id }}"
                            @if($impiego->id == old('impiego')) selected @endif
                        >
                            {{ $impiego->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="tipologia">Tipologia</label>
                <select class="form-control" id="tipologia" name="tipologia">
                    <option hidden disabled selected value="">Scegli...</option>
                    @foreach ($tipologie as $tipologia)
                        <option
                            value="{{ $tipologia->id }}"
                            @if($tipologia->id == old('tipologia')) selected @endif
                        >
                            {{ $tipologia->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="alimentazione">Alimentazione</label>
                <select
                    class="form-control"
                    id="alimentazione"
                    name="alimentazione"
                >
                    <option hidden disabled selected value="">Scegli...</option>
                    @foreach ($alimentazioni as $alimentazione)
                        <option
                            value="{{ $alimentazione->id }}"
                            @if($alimentazione->id == old('alimentazione')) selected @endif
                        >
                            {{ $alimentazione->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="posti">Numero posti</label>
                <input
                    type="number"
                    class="form-control"
                    name="posti"
                    value="{{ old("posti") }}"
                />
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary" align="right" type="submit">
                    Salva
                </button>
            </div>
        </div>
    </form>
@endsection
