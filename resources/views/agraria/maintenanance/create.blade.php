@extends("agraria.index")

@section("title", "Manutenzione")

@section("content")
    @include("partials.header", ["title" => "Nuova Manutenzione"])
    <form action="{{ route("agraria.maintenanace.store") }}" method="post">
        @csrf
        <div class="row mb-3">
            <div class="col-md-9 mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label" for="mezzo">Mezzo</label>
                        <select class="form-control" name="mezzo" id="mezzo">
                            <option value="" hidden>Seleziona il mezzo</option>
                            @foreach ($mezzi as $m)
                                <option
                                    value="{{ $m->id }}"
                                    @if ($m->id == old("mezzo")) selected @endif
                                >
                                    {{ $m->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="data">Data</label>
                        <input
                            name="data"
                            type="date"
                            class="form-control"
                            placeholder="Seleziona la data"
                            value="{{ old("data") }}"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="ore">Ore</label>
                        <input
                            name="ore"
                            type="number"
                            class="form-control"
                            placeholder="Ore totali del mezzo"
                            value="{{ old("ore") }}"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label" for="spesa">Spesa</label>
                        <div class="input-group">
                            <input
                                name="spesa"
                                class="form-control"
                                type="number"
                                placeholder="Spesa totale"
                            />
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="persona">Persona</label>
                        <input
                            name="persona"
                            class="form-control"
                            type="text"
                            placeholder="Persona che ha fatto i lavori"
                            value="{{ old("persona") }}"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label class="form-label" for="extra">
                            Manutenzioni Straordinarie
                        </label>
                        <textarea
                            name="straordinarie"
                            class="form-control"
                            rows="3"
                            placeholder="Inserire lavori straordinari eseguiti sul mezzo"
                        ></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-mod">
                    <div class="card-header">
                        <h3 class="card-title">Manutenzioni Programmate</h3>
                    </div>
                    <div class="card-body">
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
        </div>
        <div class="row">
            <div class="col">
                <button type="submit" class="btn btn-success">Salva</button>
            </div>
        </div>
    </form>
@endsection
