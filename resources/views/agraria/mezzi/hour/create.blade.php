@extends("agraria.index")

@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => "Aggiorna Ore"])
    <div class="container">
        <form
            action="{{ route("agraria.vehicle.hour.create") }}"
            method="post"
        >
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50%">Nome</th>
                        <th width="25%">Ore Effettuate</th>
                        <th width="25%">Nuove Ore</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mezzi as $m)
                        <tr>
                            <td>
                                {{ $m->nome }}
                            </td>
                            <td>
                                {{ $m->tot_ore }}
                            </td>
                            <td>
                                <input
                                    class="form-control"
                                    type="number"
                                    name="{{ "id" . $m->id }}"
                                    value="{{ old("id" . $m->id) }}"
                                />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-success btn-block" type="submit">
                Salva
            </button>
        </form>
    </div>
@endsection
