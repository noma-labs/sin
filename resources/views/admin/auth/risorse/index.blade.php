@extends("admin.index")
@section("title", "| Risorse")

@section("content")
    @include("partials.header", ["title" => "Gestione Permessi"])

    <div class="table-responsive col-md-8 offset-md-2">
        <table class="table table-bordered table-striped">
            <thead class="thead-inverse">
                <tr>
                    <th>Permesso</th>
                    <th>Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td>---</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
