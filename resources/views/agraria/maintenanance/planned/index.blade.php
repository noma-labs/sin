@extends("agraria.index")

@section("title", "Manutenzione")

@section("content")
    @include("partials.header", ["title" => "Gestione manutenzioni programmate"])

    <div class="table-responsive mb-3">
        <table class="table table-hover table-sm">
            <thead class="table-warning">
                <tr>
                    <th>Nome</th>
                    <th>Ore ricorrenza</th>
                </tr>
            </thead>
            <tbody class="table-primary">
                @foreach ($planned as $maintenance)
                    <tr>
                        <td>{{ $maintenance->nome }}</td>
                        <td>{{ $maintenance->ore }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
