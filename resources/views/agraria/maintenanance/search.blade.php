@extends("agraria.index")

@section("title", "Manutenzione")

@section("content")
    @include("partials.header", ["title" => "Ricerca Manutenzione"])
    <div class="mb-3 text-end">
        <a
            href="{{ route("agraria.maintenanace.create") }}"
            class="btn btn-success"
        >
            + Nuova Manutenzione
        </a>
    </div>
    <form
        action="{{ route("agraria.maintenanace.search.show") }}"
        method="get"
    >
        @csrf
        <div class="row mb-3">
            <div class="col-md-2">
                <label class="form-label" for="mezzo">Mezzo</label>
                <select name="vehicle" class="form-control" id="mezzo">
                    <option disabled selected>-- Seleziona --</option>
                    @foreach ($vehichles as $m)
                        <option value="{{ $m->id }}">{{ $m->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Da</label>
                <input type="date" class="form-control" name="from" />
            </div>
            <div class="col-md-2">
                <label class="form-label">A</label>
                <input type="date" class="form-control" name="to" />
            </div>
            <div class="col-md-2">
                <label class="form-label">Lavori</label>
                <input type="text" class="form-control" name="term" />
            </div>
            <div class="col-md-2 mt-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Cerca</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="table-warning">
                    <th>#</th>
                    <th>Mezzo</th>
                    <th>Data</th>
                    <th>Spesa</th>
                    <th>Ore</th>
                    <th>Persona</th>
                    <th>Lavori Extra</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenances as $maintenance)
                    <tr class="table-primary">
                        <td>{{ $maintenance->id }}</td>
                        <td>{{ $maintenance->mezzo->nome }}</td>
                        <td>{{ $maintenance->data }}</td>
                        <td>{{ $maintenance->spesa }}</td>
                        <td>{{ $maintenance->ore }}</td>
                        <td>{{ $maintenance->persona }}</td>
                        <td>{{ $maintenance->lavori_extra }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
