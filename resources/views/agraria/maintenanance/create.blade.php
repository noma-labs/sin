@extends("agraria.index")

@section("title", "Manutenzione")

@section("content")
    @include("partials.header", ["title" => "Nuova Manutenzione"])
    <form action="{{ route("agraria.maintenanace.store") }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="mezzo">Mezzo</label>
                        <select class="form-control" name="mezzo" id="mezzo">
                            <option value="" hidden>Seleziona il mezzo</option>
                            @foreach ($mezzi as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="data">Data</label>
                        <input
                            name="data"
                            type="date"
                            class="form-control"
                            placeholder="Seleziona la data"
                        />
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="ore">Ore</label>
                        <input
                            name="ore"
                            type="number"
                            class="form-control"
                            placeholder="Ore totali del mezzo"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="spesa">Spesa</label>
                        <div class="input-group">
                            <input
                                name="spesa"
                                class="form-control"
                                type="number"
                                placeholder="Spesa totale"
                            />
                            <div class="input-group-append">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="persona">Persona</label>
                        <input
                            name="persona"
                            class="form-control"
                            type="text"
                            placeholder="Persona che ha fatto i lavori"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group">
                        <label for="extra">Manutenzioni Straordinarie</label>
                        <textarea
                            name="extra"
                            class="form-control"
                            rows="3"
                            placeholder="Inserire lavori straordinari eseguiti sul mezzo"
                        ></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-mod">
                    <div class="card-header card-header-mod">
                        <h3 class="card-title">Manutenzioni Programmate</h3>
                    </div>
                    <div class="card-body card-body-mod">
                        <ul class="list-group list-group-flush">
                            @foreach ($programmate as $p)
                                <li class="list-group">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="programmate[]"
                                            id="{{ $p->id }}"
                                            value="{{ $p->id }}"
                                        />
                                        <label
                                            class="form-check"
                                            for="{{ $p->id }}"
                                        >
                                            {{ $p->nome }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-block">
                Salva
            </button>
        </div>
    </form>
@endsection
