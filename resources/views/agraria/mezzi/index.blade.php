@extends("agraria.index")

@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => "Mezzi Agricoli", "subtitle" => $vehicles->count()])
    <div class="mb-3 text-end">
        <a
            href="{{ route("agraria.vehicle.create") }}"
            class="btn btn-success"
        >
            + Nuovo Mezzo Agricolo
        </a>
    </div>
    <table id="table" class="table table-hover">
        <thead class="table-warning">
            <tr>
                <th>Nome</th>
                <th>Ore</th>
                <th>Numero Telaio</th>
                <th>Filtro olio</th>
                <th>Filtro gasolio</th>
                <th>Filtro Servizi</th>
                <th>Filtro aria int-ext</th>
                <th>Gomme Anteriori</th>
                <th>Gomme Posteriori</th>
                <th>Operazioni</th>
            </tr>
        </thead>
        <tbody class="table-primary">
            @forelse ($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->nome }}</td>
                    <td>{{ $vehicle->tot_ore }}</td>
                    <td>{{ $vehicle->numero_telaio }}</td>
                    <td>{{ $vehicle->filtro_olio }}</td>
                    <td>{{ $vehicle->filtro_gasolio }}</td>
                    <td>{{ $vehicle->filtro_servizi }}</td>
                    <td>
                        {{ $vehicle->filtro_aria_int }}-{{ $vehicle->filtro_aria_ext }}
                    </td>
                    <td>
                        @if ($vehicle->gommeAnt)
                            {{ $vehicle->gommeAnt->nome }}
                        @endif
                    </td>
                    <td>
                        @if ($vehicle->gommePos)
                            {{ $vehicle->gommePos->nome }}
                        @endif
                    </td>
                    <td>
                        <a
                            href="{{ route("agraria.vehicle.show", $vehicle->id) }}"
                            class="btn btn-secondary"
                        >
                            Dettaglio
                        </a>
                    </td>
                </tr>
            @empty
                <div class="alert alert-danger">
                    <strong>Nessun risultato ottenuto</strong>
                </div>
            @endforelse
        </tbody>
    </table>
@endsection
