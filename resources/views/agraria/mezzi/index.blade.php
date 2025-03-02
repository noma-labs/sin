@extends("agraria.index")

@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => "Mezzi Agricoli", "subtitle" => {{ $mezzi->count() }} ])
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Mezzi Agricoli
                    <span class="badge text-bg-secondary">

                    </span>
                </h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mezzi as $mezzo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mezzo->nome }}</td>
                                <td>
                                    <a
                                        href="{{ route("agraria.vehicle.show", $mezzo->id) }}"
                                        class="btn btn-secondary"
                                    >
                                        Dettaglio
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
